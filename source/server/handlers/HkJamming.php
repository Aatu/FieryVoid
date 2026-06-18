<?php
/**
 * HK Jamming resolver.
 *
 * Implements the ELINT "Jamming" disruption of remote-controlled fighter flights
 * (Orieni Hunter-Killers). An ELINT allocates JAM EW to an enemy remoteControl
 * flight during Initial Orders (see shipTooltipInitialOrdersMenu.js / EW pipeline).
 * In the CRITICAL phase, each jammed flight rolls on a disruption table; outcomes
 * range from a next-turn initiative penalty, through losing control ("Uncontrolled"),
 * up to one or two craft dropping out of the flight.
 *
 * Called once from the tail of Criticals::setCriticals (after both crit passes and
 * after firing has resolved, so the impact exemption can read this turn's ram result).
 *
 * Replay-deterministic: the d20 roll is read from a per-flight hkJammingRoll note
 * (Fighter::onIndividualNotesLoaded → jammingLoadedTurn/Value) when present for the
 * current turn, and only rolled fresh + persisted on the live Fire Phase advance.
 * setCriticals re-runs on replay and Dice::d is non-deterministic, so this is required.
 *
 * Disruption table (d20 + 1 per JAM point beyond the first):
 *   1-14 : no effect
 *   15-16: -2 ini next turn          (1x ReducedIniativeOneTurn)
 *   17-18: -4 ini next turn          (2x ReducedIniativeOneTurn)
 *   19-20: Control Lost for 1 turn   (Uncontrolled + 2x ReducedIniativeOneTurn)
 *   21   : Control Lost + 1 drops out (above + 1x DisengagedFighter on last live craft)
 *   22+  : Control Lost + 2 drop out  (above + 2x DisengagedFighter on last live craft)
 *
 * "Jamming counts as OEW for DIST" is handled separately in BaseShip::getOEWTargetNum.
 */
class HkJamming
{
    const JAM_RANGE = 30; //hexes; ELINT must be within 30 in EW phase AND crit phase

    /* One-shot per advance: guards against the resolver running more than once if
     * setCriticals were ever re-entered within a single request. */
    public static $alreadyResolved = false;

    public static function resolveJamming($gamedata)
    {
        if (self::$alreadyResolved) return;
        self::$alreadyResolved = true;

        foreach ($gamedata->ships as $flight) {
            if (!($flight instanceof FighterFlight)) continue;
            if (empty($flight->remoteControl)) continue;
            if ($flight->isDestroyed()) continue;

            self::resolveForFlight($flight, $gamedata);
        }
    }

    private static function resolveForFlight($flight, $gamedata)
    {
        //Total JAM aimed at this flight, summed across all enemy ELINTs that are
        //in range NOW (crit phase). EW entries already encode the EW-phase allocation;
        //requiring current-position range enforces the "within 30 in both phases" rule
        //(an ELINT that drifted out of range this turn no longer contributes).
        $totalJam = 0;
        foreach ($gamedata->ships as $elint) {
            if ($elint->id === $flight->id) continue;
            if (!$elint->isElint()) continue;
            if ($elint->team === $flight->team) continue; //jamming is hostile
            if (Mathlib::getDistanceHex($flight, $elint) > self::JAM_RANGE) continue;

            $totalJam += $elint->getEWbyType("JAM", $gamedata->turn, $flight);
        }

        if ($totalJam < 1) return; //no jamming applied → no roll

        //Impact exemption: "if the targeted H-K fight impacts its target, no EW
        //allocated against it on that turn has any effect." A ram that connected
        //this turn shows as a RammingAttack fire order with shotshit > 0 (firing
        //has already resolved by the time setCriticals runs).
        if (self::flightImpactedThisTurn($flight, $gamedata)) return;

        //Replay-deterministic d20.
        $roll = self::nextJammingRoll($flight, $gamedata);
        $modified = $roll + ($totalJam - 1); //+1 per JAM point beyond the first

        self::applyTableResult($flight, $modified, $roll, $totalJam, $gamedata);
    }

    /* Apply the disruption table. Crits are placed on the flight's sample fighter
     * (systems[1] — flights have no CnC, and getCommonIniModifiers reads flight ini
     * crits from the sample fighter). DisengagedFighter drops the LAST still-live
     * craft so the flight shrinks from the tail, matching the spec. */
    private static function applyTableResult($flight, $modified, $rawRoll, $totalJam, $gamedata)
    {
        $sample = $flight->getSampleFighter();
        if (!$sample) return;

        $iniPenalties = 0;   //number of ReducedIniativeOneTurn crits to add (-10 each)
        $uncontrolled = false;
        $dropouts     = 0;

        if ($modified >= 22) {
            $uncontrolled = true; $iniPenalties = 2; $dropouts = 2;
        } else if ($modified >= 21) {
            $uncontrolled = true; $iniPenalties = 2; $dropouts = 1;
        } else if ($modified >= 19) {
            $uncontrolled = true; $iniPenalties = 2;
        } else if ($modified >= 17) {
            $iniPenalties = 2;
        } else if ($modified >= 15) {
            $iniPenalties = 1;
        } else {
            //1-14: no effect. Still log the (failed) attempt for the combat log.
            self::logResult($flight, "Jamming had no effect", $rawRoll, $modified, $totalJam, $gamedata);
            return;
        }

        for ($i = 0; $i < $iniPenalties; $i++) {
            self::addCrit($sample, $flight, "ReducedIniativeOneTurn", $gamedata);
        }
        if ($uncontrolled) {
            self::addCrit($sample, $flight, "Uncontrolled", $gamedata);
        }
        for ($i = 0; $i < $dropouts; $i++) {
            self::dropLastLiveCraft($flight, $gamedata);
        }

        $desc = self::resultDescription($iniPenalties, $uncontrolled, $dropouts);
        self::logResult($flight, $desc, $rawRoll, $modified, $totalJam, $gamedata);
    }

    private static function resultDescription($iniPenalties, $uncontrolled, $dropouts)
    {
        $parts = array();
        if ($iniPenalties > 0) $parts[] = (-5 * $iniPenalties) . " Initiative next turn";
        if ($uncontrolled)     $parts[] = "Control Lost (Uncontrolled, -15 Initiative)";
        if ($dropouts > 0)     $parts[] = $dropouts . ($dropouts === 1 ? " A fighter drops out" : " fighters drop out");
        return implode(", ", $parts);
    }

    /* Add a disruption crit to a fighter system, forced to persist via setCriticals'
     * normal getUpdatedCriticals sweep. turn = currentTurn; oneturn crits take effect
     * NEXT turn (hasCritical checks turn+1), matching the rules' "next turn" wording. */
    private static function addCrit($system, $flight, $phpclass, $gamedata)
    {
        $crit = new $phpclass(-1, $flight->id, $system->id, $phpclass, $gamedata->turn);
        $crit->updated = true;
        $crit->newCrit = true; //force save even though it takes effect next turn
        $system->criticals[] = $crit;
    }

    /* Drop the last still-active craft in the flight by applying a DisengagedFighter
     * crit (permanent — turnend 0). Mirrors how combat dropout / Hangar Ops disengage
     * fold a craft out of the flight (isDestroyed picks up DisengagedFighter). */
    private static function dropLastLiveCraft($flight, $gamedata)
    {
        //Iterate by VALUE (reversed) — $flight->systems may be sparsely keyed after
        //damage/dropout processing, so positional indexing (systems[count-1]) can hit
        //an undefined key. Walk the actual craft from the tail and drop the last live one.
        foreach (array_reverse(array_values($flight->systems)) as $craft) {
            if (!($craft instanceof Fighter)) continue;
            if ($craft->isDestroyed($gamedata->turn)) continue;

            $crit = new DisengagedFighter(-1, $flight->id, $craft->id, "DisengagedFighter", $gamedata->turn);
            $crit->updated = true;
            $crit->newCrit = true;
            $craft->criticals[] = $crit;
            return;
        }
    }

    /* True when the flight landed a ram this turn (impact exemption). Ramming has
     * already resolved in Firing::fireWeapons before setCriticals, so a connecting
     * ram shows as a RammingAttack fire order with shotshit > 0 for this turn. */
    private static function flightImpactedThisTurn($flight, $gamedata)
    {
        foreach ($flight->getAllFireOrders($gamedata->turn) as $fire) {
            if ($fire->turn != $gamedata->turn) continue;
            $weapon = $flight->getSystemById($fire->weaponid);
            if (!$weapon || empty($weapon->isRammingAttack)) continue;
            if ($fire->shotshit > 0) return true;
        }
        return false;
    }

    /* ---- Replay-deterministic roll (mirrors HangarOps inadequate-roll FIFO) ---- */

    private static function nextJammingRoll($flight, $gamedata)
    {
        $sample = $flight->getSampleFighter();
        $turn = (int)$gamedata->turn;

        //Replay path: a value loaded for THIS turn was taken on the live advance.
        if ($sample && (int)$sample->jammingLoadedTurn === $turn) {
            return (int)$sample->jammingLoadedValue;
        }

        //Live path: roll fresh and persist.
        $roll = Dice::d(20);
        self::recordJammingRoll($flight, $sample, $roll, $gamedata);
        return $roll;
    }

    private static function recordJammingRoll($flight, $sample, $roll, $gamedata)
    {
        if (!$sample) return;
        $sample->jammingLoadedTurn  = (int)$gamedata->turn;
        $sample->jammingLoadedValue = (int)$roll;

        $payload = json_encode(array('roll' => (int)$roll, 'turn' => (int)$gamedata->turn));
        $note = new IndividualNote(
            -1,
            $gamedata->id,
            $gamedata->turn,
            $gamedata->phase,
            $flight->id,
            $sample->id,
            'hkJammingRoll',
            'HK Jamming disruption roll',
            $payload
        );
        Manager::insertIndividualNote($note);
    }

    /* Surface the result in the combat log via the flight's RammingAttack system,
     * the self-targeted convention used by marine missions / Inadequate Hangars. */
    private static function logResult($flight, $desc, $rawRoll, $modified, $totalJam, $gamedata)
    {
        $rammingSystem = $flight->getSystemByName("RammingAttack");
        if (!$rammingSystem) return;

        $fireOrder = new FireOrder(
            -1, "normal", $flight->id, $flight->id,
            $rammingSystem->id, -1, $gamedata->turn, 1,
            100, $rawRoll, 1, 1, 0,
            0, 0, 'HkJamming', 10000
        );
        $jamMod = $totalJam - 1; //+1 per JAM point beyond the first
        $fireOrder->pubnotes = "<br>JAMMING: $totalJam Jamming point(s) applied. Rolled $rawRoll +$jamMod = $modified. Result: $desc.";
        $fireOrder->addToDB = true;
        $rammingSystem->fireOrders[] = $fireOrder;
    }
}
