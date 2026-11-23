<?php
/**
 * One submitted unit -> the server-side ship object a Phase::process() works on.
 *
 * This is the per-ship body of Manager::getShipsFromJSON, moved here unchanged (TACTICAL_AI_PLAN.md
 * section 3.2 step 4) so that every source of orders builds its ships with the SAME code:
 *   - a browser POST (Manager::submitTacGamedata -> getShipsFromJSON);
 *   - the CPU opponent (AIOrderBuilder), which writes the same plain array a browser would post;
 *   - the campaign's fleet injection (CAMPAIGN_PLAN.md section 6.2), when it is built.
 *
 * $value is ONE entry of the posted "ships" list, already json_decode'd to an array. Every field
 * is a CLAIM: the comments below say which Phase::process() checks each one, and nothing here may
 * be trusted before that.
 *
 * ⚠️ These are POST-SIDE ships: fresh instances with no enhancements applied and no notes loaded.
 * Anything that needs the unit's real state must ask the loaded gamedata's own ship
 * ($gamedata->getShipById).
 */
class ShipPayload
{
    /** The whole posted list -> ships keyed by id (or a unique key where the entry has none). */
    public static function buildShips($array)
    {
        $ships = array();
        if (!is_array($array)) return $ships;

        foreach ($array as $value) {
            $ship = self::buildShip($value);
            if ($ship === null) continue;

            $ships[$value["id"] ?? uniqid('ship_')] = $ship; //Apply a unique entry, as it seemed Loaded Fleets were overwriting a single ship sometimes.
        }

        return $ships;
    }

    /** One posted entry -> its ship, or null when the entry names no class. */
    public static function buildShip($value)
    {
        $movements = array();
        if (isset($value["movement"]) && is_array($value["movement"])) {
            foreach($value["movement"] as $i => $move) {
                $movement = new MovementOrder(
                    $move["id"] ?? -1,
                    $move["type"] ?? null,
                    new OffsetCoordinate($move["position"] ?? [0, 0]),
                    $move["xOffset"] ?? 0,
                    $move["yOffset"] ?? 0,
                    $move["speed"] ?? 0,
                    $move["heading"] ?? 0,
                    $move["facing"] ?? 0,
                    $move["preturn"] ?? false,
                    $move["turn"] ?? 0,
                    $move["value"] ?? 0,
                    $move["at_initiative"] ?? false
                );
                $movement->requiredThrust = $move["requiredThrust"] ?? 0;
                $movement->assignedThrust = $move["assignedThrust"] ?? 0;
                //Carry the 'forced' flag through the POST (Gravitic Augmenter free jinks are
                //marked forced=true). submitMovement skips forced moves so they never persist -
                //they are re-added transiently every load. Without this whitelist the flag is
                //lost and the free jink would be written to the DB, doubling next load.
                $movement->forced = $move["forced"] ?? false;

                $movements[$i] = $movement;
            }
        }

        $EW = array();
        if (isset($value["EW"]) && is_array($value["EW"])) {
            foreach($value["EW"] as $i => $EWdata) {
                $EWentry = new EWentry(
                    -1,
                    $EWdata["shipid"] ?? -1,
                    $EWdata["turn"] ?? 0,
                    $EWdata["type"] ?? "",
                    $EWdata["amount"] ?? 0,
                    $EWdata["targetid"] ?? null
                );
                $EW[$i] = $EWentry;
            }
        }

        $className = $value["phpclass"] ?? null;
        if (!$className) return null; // skip if class not defined

        /** @var BaseShip $ship */
        $ship = new $className(
            $value["id"] ?? -1,
            $value["userid"] ?? -1,
            $value["name"] ?? "Unnamed",
            $value["slot"] ?? 0
        );

			$ship->team = $value["team"] ?? 0;

        $ship->pointCostEnh = ($value["pointCostEnh"] ?? 0) + ($value["pointCostEnh2"] ?? 0);
        $ship->setMovements($movements);
        $ship->EW = $EW;
        
        if (isset($value["bulkBuy"])) {
            $ship->bulkBuy = $value["bulkBuy"];
        }

        if ($ship instanceof FighterFlight) {
            $ship->flightSize = $value["flightSize"] ?? 1;
            $ship->populate();
        }

        //Pre-battle damage (PREBATTLE_DAMAGE_PLAN.md §4.3). Carried RAW here and
        //validated by PreBattleDamage::sanitise at the one place it is consumed -
        //BuyingGamePhase::process. Every other phase ignores the field, so a client
        //cannot inject damage mid-game.
        //⚠️ Must stay AFTER the populate() above: flight ordinals resolve by position
        //in $ship->systems, so the fighters have to exist first.
        $ship->preBattleDamage = $value["preBattleDamage"] ?? array();

        /* Reinforcements (REINFORCEMENTS_PLAN.md §4 Stage 1). Carried RAW here and consumed by
           BuyingGamePhase::process alone, which checks the game rule and only then writes the
           real $ship->reinforcement. Every other phase ignores the field, so a client cannot
           flag a unit into hyperspace mid-game.
           ⚠️ DELIBERATELY NOT $ship->reinforcement. That property is read by getTurnDeployed
           and getTurnPlaced, and a POST-side ship carries no $arrivalTurn - so writing it here
           would make every POSTed reinforcement answer 999 to both accessors in every phase,
           silently early-returning Hangar::generateIndividualNotes and
           HangarOps::validateDeployBayOrders, neither of which resolves through
           $gamedata->getShipById() the way DeploymentGamePhase::validateDeployment now does.
           See BaseShip::$reinforcementClaim and plan trap 3.
           ⚠️ The saved-fleet parser above (getSavedShipsFromJSON) writes the REAL property from
           the same wire key, and the difference is the point: those ships are built, sanitised,
           written to tac_saved_ship and thrown away without ever entering a TacGamedata, so
           neither turn accessor can be reached. These ships do enter one. */
        $ship->reinforcementClaim = !empty($value["reinforcement"]);

        /* THE MANIFEST (REINFORCEMENTS_PLAN.md §3.5, Stage 5) - the ONE reinforcement field a
           client is trusted to send in a live game, and even then only as a claim.
           InitialOrdersGamePhase::process re-validates it against the SERVER-side ships and
           writes NULL for anything it does not believe.

           ⚠️ $arrivalTurn IS DELIBERATELY NOT WHITELISTED and must never be. It is written only
           by the end-of-formation-turn deviation sweep; a client that could set it could bring
           its own fleet out of hyperspace a turn early, at a hex of its choosing, with no
           exit in between. $reinforcement is not whitelisted here either - see the claim
           property above.

           (int) rather than a raw carry: mysqli and JSON disagree about number types, and the
           validation downstream compares it against real ship ids. 0 is normalised to null so
           "unassigned" has exactly one representation. */
        $arrivalVia = isset($value["arrivalVia"]) ? (int)$value["arrivalVia"] : 0;
        $ship->arrivalVia = ($arrivalVia > 0) ? $arrivalVia : null;

        /* HYPERSPACE_IMPROVEMENTS_PLAN.md STAGE H6a/H6b - the arrival SPEED and the carrier to START
           IN, beside the berth they belong to, and the same kind of thing: a claim.
           InitialOrdersGamePhase::persistManifest clamps the speed, re-fits the hangar and writes
           what it believes; nothing else reads them. Only meaningful with a berth. */
        if ($ship->arrivalVia !== null && isset($value["arrivalSpeed"])) {
            $hangar = isset($value["arrivalHangar"]) ? (int)$value["arrivalHangar"] : 0;
            $ship->setArrivalOrderClaim((int)$value["arrivalSpeed"], ($hangar > 0) ? $hangar : null);
        }

        $ship->enhancementOptions = $value["enhancementOptions"] ?? [];

        /* Per-system enhancements (WEAPON_ENHANCEMENTS_PLAN.md §4.5). Carried RAW here and
           validated by Enhancements::sanitiseSystemEnhancements at the one place it is
           consumed - BuyingGamePhase::process - which re-resolves every systemid, re-checks
           eligibility and REPLACES the client's prices with the server's own (D4). Every
           other phase ignores the field, so a client cannot inject a refit mid-game. */
        $ship->systemEnhancements = $value["systemEnhancements"] ?? array();

        /* OFFICERS_PLAN.md Stage 10 - officers the player ordered to another post this turn. A CLAIM,
           carried raw: InitialOrdersGamePhase::process checks each against the loaded ship
           (Officers::saveTransfers) before writing it, and every other phase ignores the field. */
        $ship->setOfficerTransferClaims($value["officerTransfers"] ?? array());

        /* FIGHTER_MISSIONS_PLAN.md §3.7 - the mission the player picked for a flight this turn, {m, k}. A CLAIM,
           carried raw: InitialOrdersGamePhase::process checks it against the loaded flight (FighterMissions::
           saveClaim) before writing it, and every other phase ignores the field. */
        if ($ship instanceof FighterFlight) $ship->setFighterMissionClaim($value["fighterMission"] ?? null);

        /* OFFICERS_PLAN.md Stage 36 - an Expert Electrician's split of this turn's shunted power, {ob, thrust}. A CLAIM, carried raw:
           InitialOrdersGamePhase::process checks it against the loaded flight (Officers::saveElectricianShunt); every other phase ignores it. */
        if ($ship instanceof FighterFlight) $ship->setElectricianClaim($value["electricianShunt"] ?? null);

        /* OFFICERS_PLAN.md Stage 35 - an Expert Analyst's scan this turn, {a: ability, t: target id}. A CLAIM, carried raw:
           InitialOrdersGamePhase::process checks it against the loaded ship (Officers::saveAnalystScan); every other phase ignores it. */
        $ship->setAnalystScanClaim($value["analystScan"] ?? null);

        $systems = $value["systems"] ?? [];
        foreach ($systems as $i => $system) {
            $sys = $ship->getSystemById($i);

            if (isset($system["power"]) && is_array($system["power"])) {
                foreach ($system["power"] as $power) {
                    $powerEntry = new PowerManagementEntry(
                        $power["id"] ?? -1,
                        $power["shipid"] ?? -1,
                        $power["systemid"] ?? -1,
                        $power["type"] ?? "",
                        $power["turn"] ?? 0,
                        $power["amount"] ?? 0
                    );
                    if ($sys) {
                        $sys->setPower($powerEntry);
                    }
                }
            }

            if (isset($system["fireOrders"]) && is_array($system["fireOrders"])) {
                $fires = [];
                foreach($system["fireOrders"] as $fo) {
                    $fireOrder = new FireOrder(
                        -1,
                        $fo["type"] ?? "",
                        $fo["shooterid"] ?? -1,
                        $fo["targetid"] ?? -1,
                        $fo["weaponid"] ?? -1,
                        $fo["calledid"] ?? -1,
                        $fo["turn"] ?? 0,
                        $fo["firingMode"] ?? 1,
                        0, 0, $fo["shots"] ?? 0, 0, 0,
                        $fo["x"] ?? 0,
                        $fo["y"] ?? 0,
                        $fo["damageclass"] ?? null
                    );
                    //Carry the client-supplied notes through. The FireOrder ctor doesn't
                    //take notes, and weapons that need to pass custom per-shot data to
                    //server-side resolution (e.g. Hypergraviton Blaster transfer-target
                    //list) encode it here. Safe: server-side notes writes happen later
                    //during firing resolution as appends/overwrites; nothing reads the
                    //incoming client notes value except weapons that opt in.
                    $fireOrder->notes = $fo["notes"] ?? "";
                    if ($sys) {
                        $fires[] = $fireOrder;
                    }
                }
                if ($sys) $sys->setFireOrders($fires);
            }

            if (isset($system["systems"]) && is_array($system["systems"])) {
                foreach ($system["systems"] as $fightersys) {
                    $fig = $sys ? $sys->getSystemById($fightersys["id"] ?? -1) : null;
                    if (!$fig) continue;

                    if (isset($fightersys["fireOrders"]) && is_array($fightersys["fireOrders"])) {
                        $fires = [];
                        foreach($fightersys["fireOrders"] as $fo) {
                            $fireOrder = new FireOrder(
                                -1,
                                $fo["type"] ?? "",
                                $fo["shooterid"] ?? -1,
                                $fo["targetid"] ?? -1,
                                $fo["weaponid"] ?? -1,
                                $fo["calledid"] ?? -1,
                                $fo["turn"] ?? 0,
                                $fo["firingMode"] ?? 1,
                                0, 0, $fo["shots"] ?? 0, 0, 0,
                                $fo["x"] ?? 0,
                                $fo["y"] ?? 0,
                                $fo["damageclass"] ?? null
                            );
                            //Carry client-supplied notes through the FIGHTER branch too (the
                            //main-ship branch already does this). MinorThoughtPulsar encodes its
                            //free thrust allocation as "MTP|hit|shots|dmg" here; read in
                            //beforeFiringOrderResolution. Safe: server-side notes are written later
                            //during firing resolution; nothing reads incoming notes except opt-in weapons.
                            $fireOrder->notes = $fo["notes"] ?? "";
                            $fires[] = $fireOrder;
                        }

                        // ammo transfer
                        if (isset($fightersys["ammo"])) {
                            foreach ($fightersys["ammo"] as $i => $ammo) {
                                if (isset($ammo)) {
                                    $fig->setAmmo($i, $ammo);
                                }
                            }
                        }

                        $fig->setFireOrders($fires);
                    }

                    if (isset($fightersys["individualNotesTransfer"])) {
                        $fig->individualNotesTransfer = $fightersys["individualNotesTransfer"];
                        $fig->doIndividualNotesTransfer();
                    }

                    /* Some fighter systems CAN be boosted now.
                       ⚠️ ONE setPower() PER ENTRY (fixed 2026-09-11). ShipSystem::setPower APPENDS
                       whatever it is given, and this used to hand it the whole list - twice, because
                       the block was duplicated - so a POST-side fighter system's $power held NESTED
                       ARRAYS instead of entries. DBManager::submitPower quietly flattens that shape,
                       which is why nothing noticed until something asked a POST-side fighter's power a
                       question: JumpEngine::isOverloading, via
                       InitialOrdersGamePhase::dropFireOfJumpingShip, fataled with "Attempt to read
                       property turn on array" (game 4347). The shape now matches the DB-loaded one,
                       which also sets one entry at a time. */
                    if (isset($fightersys["power"]) && is_array($fightersys["power"])) {
                        foreach ($fightersys["power"] as $p) {
                            $fig->setPower(new PowerManagementEntry(
                                $p["id"] ?? -1,
                                $p["shipid"] ?? -1,
                                $p["systemid"] ?? -1,
                                $p["type"] ?? "",
                                $p["turn"] ?? 0,
                                $p["amount"] ?? 0
                            ));
                        }
                    }
                }
            }

            if (isset($system["individualNotesTransfer"])) {

                if ($sys) {
                    $sys->individualNotesTransfer = $system["individualNotesTransfer"];
                    $sys->doIndividualNotesTransfer();
                }
            }
        }

        return $ship;
    }
}
