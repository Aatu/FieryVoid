<?php
/**
 * CPU opponent - flights that start the battle in a hangar, and their launch (TACTICAL_AI_PLAN.md 10.10, "Missing systems" M2).
 *
 * The Orieni Hunter-Killers (FighterFlight::$deploysInHangar, $remoteControl) must start the battle DOCKED where the fleet has room
 * for them - the page refuses a commit that leaves one on the map while a carrier could take it (gamedata.js doCommit) - and are
 * launched from there. The user's notes (2026-11-24): "the ai player can just launch HKs up to the capacity of their carrier from
 * Turn 1 onwards until they are all in the field. When placing HKs in Hangars during deployment, spreading them out amongst the
 * different carriers (where possible) is wise as it lets them launch quicker and prevents them being destroyed in Hangar if the
 * Carrier gets taken out on Turn 1."
 *
 * DEPLOYMENT (dockPlan / dock): each such flight placing this turn goes into a carrier of its slot placing with it and arriving on the
 * same turn (HangarOps::validateDeployBayOrders' gates), in ONE bay that takes its category and class with the room for the whole
 * flight (HangarOps' own capacity tests - effective capacity, the boxes used, the category cap the carrier declares). The carrier
 * chosen is the one that would launch everything given it soonest (its bays' launch rate), then the one holding fewest such flights:
 * the flights spread over the carriers. A flight that fits no single bay may span several of one carrier (the server's re-home lays
 * it out) - and that carrier takes no more, so no bay is counted twice. A flight no carrier can hold is placed on the map as before.
 * The order is the page's: a `deployStarts` entry on the bay (Hangar::doIndividualNotesTransfer), one order with no count - the
 * server fills the bay, then any other (HangarOps::performDeployStartDockFromOrders) - and no deploy row for the flight
 * (DeploymentGamePhase::validateDeployment exempts it).
 *
 * LAUNCH (launches, Fire phase - processed at the end of the turn, the flight on the board next turn): every such flight docked in a
 * carrier of the bot is launched, as many craft as the bays' launch rates allow - the page's budget (confirm.js hangarLaunchNoSplit:
 * a bay's output less what it has launched or recovered this turn; a flight spanning bays is drawn from the smallest first). A flight
 * the budget cannot take whole goes out in part (the page's partial launch - a "Split" flight), the rest next turn. A carrier that is
 * pivoting or rolling launches nothing (HangarOps::canLaunchWholeFlight refuses it). The order is the page's too: {phpclass, size,
 * dockedFlightId} in the `launches` list of the bay holding the flight's entry.
 *
 * HOME TO RELOAD (stage T9, M11 - the user, 2026-11-23: "if possible ... order fighters with empty gatling guns to go and reload in
 * carriers"; ruled 2026-11-24: home when DRY, out again when FULL, and no Ordnance Reserve bought for an auto-composed carrier). A flight
 * of the bot with no usable gun left (Posture::usableGuns) goes home (home()) to the nearest carrier with a bay that holds it whole and
 * where the carrier's servicing would reload it (reloadDue - the engine's three whileDocked rules: a gatling's free round a turn, an old
 * rack's or a magazine's missiles only from the carrier's Ordnance Reserve). FlightDecider flies it there; in the Fire phase it docks
 * where it ended its move, in a bay the server's own test takes it in (docks(): HangarOps::canShipReceive) - a `docks` order on the bay,
 * {flightId, count}, beside any launches (docks are resolved first and share the bay's rate). Docked, it is launched again with the
 * others (launches()) once nothing more would reload: full, or the reserve spent.
 * Any other docked craft are left where they are.
 */
class HangarDecider
{
    //What the last dockPlan() / launches() did, for tac_ai_log (stage T9, M11: 'docking' - flights ordered to dock this Fire phase).
    public static $last = array('docked' => 0, 'carriers' => 0, 'onMap' => 0, 'launched' => 0, 'craft' => 0, 'held' => 0, 'docking' => 0);

    /* Stage T9, M11: the AMMO_F* enhancements a magazine restocks and their rounds - a COPY of AmmoMagazine::whileDocked's own map (a
       function-static there). Change one, change both. */
    const MAGAZINE_ROUNDS = array('AMMO_FB' => 'AmmoMissileFB', 'AMMO_FL' => 'AmmoMissileFL', 'AMMO_FH' => 'AmmoMissileFH',
        'AMMO_FY' => 'AmmoMissileFY', 'AMMO_FD' => 'AmmoMissileFD', 'AMMO_DUM' => 'AmmoMissileFDum');

    //Must $unit start the battle in a hangar where there is room (the Orieni Hunter-Killers)?
    public static function startsDocked($unit)
    {
        return $unit instanceof FighterFlight && !empty($unit->deploysInHangar);
    }

    // ------------------------------------------------------------------ deployment

    /* Where the bot's flights that start docked go this Deployment phase: flight id => array('carrier' => ship, 'hangar' => Hangar).
       A flight with no room anywhere is left out (placed on the map). */
    public static function dockPlan(TacGamedata $gamedata, $botId)
    {
        $flights = array();
        $carriers = array();
        foreach ($gamedata->ships as $unit) {
            if ((int)$unit->userid !== (int)$botId || !self::placingNow($unit, $gamedata)) continue;
            if (self::startsDocked($unit)) $flights[] = $unit;
            else if (!($unit instanceof FighterFlight) && count(HangarOps::collectHangars($unit)) > 0) $carriers[] = $unit;
        }
        self::$last['docked'] = 0;
        self::$last['carriers'] = 0;
        self::$last['onMap'] = 0;
        if (count($flights) == 0) return array();

        //the biggest flights first: they need the most room
        $turn = (int)$gamedata->turn;
        usort($flights, function ($a, $b) use ($turn) {
            $na = $a->countActiveCraft($turn);
            $nb = $b->countActiveCraft($turn);
            return ($na !== $nb) ? $nb - $na : $a->id - $b->id;
        });

        $reserved = array();  //hangar id => boxes promised this pass
        $catUsed = array();   //carrier id => category => craft promised this pass
        $given = array();     //carrier id => array('craft', 'flights')
        $closed = array();    //carrier id => true: a flight spans its bays, it takes no more
        $plan = array();
        foreach ($flights as $flight) {
            $best = null;
            foreach ($carriers as $carrier) {
                $cid = (int)$carrier->id;
                if (isset($closed[$cid]) || !self::sameArrival($flight, $carrier, $gamedata)) continue;
                if ((int)$flight->slot !== (int)$carrier->slot) continue;
                $fit = self::fit($carrier, $flight, $reserved, isset($catUsed[$cid]) ? $catUsed[$cid] : array(), $turn);
                if ($fit === null) continue;

                $n = $flight->countActiveCraft($turn);
                $had = isset($given[$cid]) ? $given[$cid] : array('craft' => 0, 'flights' => 0);
                $rate = max(1, $fit['rate']);
                $turns = (int)ceil(($had['craft'] + $n) / $rate);
                $key = array($turns, $had['flights'], -$fit['capLeft'], $cid);
                if ($best === null || $key < $best['key']) $best = array('key' => $key, 'carrier' => $carrier, 'fit' => $fit);
            }
            if ($best === null) {
                self::$last['onMap']++;
                continue;
            }

            $carrier = $best['carrier'];
            $cid = (int)$carrier->id;
            $n = $flight->countActiveCraft($turn);
            foreach ($best['fit']['boxes'] as $hid => $boxes) $reserved[$hid] = (isset($reserved[$hid]) ? $reserved[$hid] : 0) + $boxes;
            $category = strtolower(trim((string)HangarOps::trueSizeOf($flight)));
            $catUsed[$cid][$category] = (isset($catUsed[$cid][$category]) ? $catUsed[$cid][$category] : 0) + $n;
            if (!isset($given[$cid])) $given[$cid] = array('craft' => 0, 'flights' => 0);
            $given[$cid]['craft'] += $n;
            $given[$cid]['flights']++;
            if (count($best['fit']['boxes']) > 1) $closed[$cid] = true;
            $plan[(int)$flight->id] = array('carrier' => $carrier, 'hangar' => $best['fit']['hangar']);
        }
        self::$last['docked'] = count($plan);
        self::$last['carriers'] = count($given);
        return $plan;
    }

    /* The bot's Deployment entries with the plan applied: no deploy row for a flight going into a hangar, and the `deployStarts`
       orders on the bays they go into. */
    public static function dock(array $entries, array $plan)
    {
        if (count($plan) == 0) return $entries;
        $orders = array(); //carrier id => hangar id => list of {flightId}
        foreach ($plan as $flightId => $place) $orders[(int)$place['carrier']->id][(int)$place['hangar']->id][] = array('flightId' => (int)$flightId);

        foreach ($entries as $index => $entry) {
            $id = (int)$entry['id'];
            if (isset($plan[$id])) $entries[$index]['movement'] = array();
            if (!isset($orders[$id])) continue;
            foreach ($orders[$id] as $hangarId => $list) {
                $entries[$index]['systems'][$hangarId] = array('id' => $hangarId, 'individualNotesTransfer' => json_encode(array('deployStarts' => $list)));
            }
        }
        return $entries;
    }

    //Is $unit placing this turn, not yet placed, and not an arrival the server places itself?
    private static function placingNow($unit, TacGamedata $gamedata)
    {
        if ($unit->isDestroyed() || $unit->isTerrain() || !empty($unit->removed)) return false;
        if ($unit->getTurnPlaced($gamedata) != $gamedata->turn) return false;
        if (JumpEngine::isArrivingReinforcement($unit, $gamedata)) return false;
        foreach ($unit->movement as $move) {
            if ($move->type == 'deploy' && $move->turn == $gamedata->turn) return false;
        }
        return true;
    }

    //HangarOps::validateDeployBayOrders: the two must arrive on the same turn (both are placing now - placingNow).
    private static function sameArrival($flight, $carrier, TacGamedata $gamedata)
    {
        return $flight->getTurnDeployed($gamedata) == $carrier->getTurnDeployed($gamedata);
    }

    /* Can $carrier take the whole of $flight, beside what is already promised ($reserved boxes per hangar, $catUsed craft per
       category of this carrier)? array('hangar' => the bay the order goes on, 'boxes' => hangar id => boxes taken, 'rate' => the
       launch rate of the bays that take the flight's category, 'capLeft' => category room left after it) or null. One bay that holds
       it whole when there is one (the biggest such); else the bays together, biggest first, as the server's re-home lays it out. */
    private static function fit($carrier, $flight, array $reserved, array $catUsed, $turn)
    {
        $n = $flight->countActiveCraft($turn);
        if ($n <= 0) return null;
        $category = HangarOps::trueSizeOf($flight);
        $catKey = strtolower(trim((string)$category));
        $capLeft = HangarOps::categoryCapRemaining($carrier, $category);
        if ($capLeft !== PHP_INT_MAX) $capLeft -= isset($catUsed[$catKey]) ? $catUsed[$catKey] : 0;
        if ($capLeft < $n) return null;

        $bpc = HangarOps::boxesPerCraftForClass($flight->phpclass);
        if ($bpc <= 0) $bpc = 1;
        $bays = array();
        $rate = 0;
        foreach (HangarOps::collectHangars($carrier) as $h) {
            if (!empty($h->isLCVRail) || !empty($h->isShadowHangar) || !empty($h->isDockingBay)) continue;
            $isCat = !empty($h->isCatapult);
            if (!$isCat && $h->isDestroyed()) continue;
            if (!HangarOps::hangarAcceptsCategory($h, $category, $carrier) || !HangarOps::hangarAcceptsFighterClass($h, $flight)) continue;
            $rate += max(0, (int)$h->output);
            $free = HangarOps::effectiveCapacity($h) - HangarOps::occupiedBoxes($h, $carrier) - (isset($reserved[(int)$h->id]) ? $reserved[(int)$h->id] : 0);
            $craft = $isCat ? $free : (int)floor($free / $bpc);
            if ($craft > 0) $bays[] = array('hangar' => $h, 'craft' => $craft, 'cat' => $isCat);
        }
        if (count($bays) == 0) return null;
        usort($bays, function ($a, $b) { return ($a['craft'] !== $b['craft']) ? $b['craft'] - $a['craft'] : $a['hangar']->id - $b['hangar']->id; });

        $boxes = function ($bay, $craft) use ($bpc) { return $bay['cat'] ? $craft : (int)ceil($craft * $bpc); };
        $capAfter = ($capLeft === PHP_INT_MAX) ? PHP_INT_MAX : $capLeft - $n;
        foreach ($bays as $bay) { //the biggest bay holding it whole
            if ($bay['craft'] >= $n) return array('hangar' => $bay['hangar'], 'boxes' => array((int)$bay['hangar']->id => $boxes($bay, $n)), 'rate' => $rate, 'capLeft' => $capAfter);
        }
        $taken = array();
        $left = $n;
        foreach ($bays as $bay) {
            if ($left <= 0) break;
            $take = min($left, $bay['craft']);
            $taken[(int)$bay['hangar']->id] = $boxes($bay, $take);
            $left -= $take;
        }
        if ($left > 0) return null;
        return array('hangar' => $bays[0]['hangar'], 'boxes' => $taken, 'rate' => $rate, 'capLeft' => $capAfter);
    }

    // ------------------------------------------------------------------ launch (Fire phase)

    /* The hangar orders for this Fire phase: unit id => hangar id => array('id', 'individualNotesTransfer'), for FireDecider's
       entries. Every flight that started docked by rule goes, and (stage T9, M11) every flight the bot sent home once nothing more would
       reload, as far as the bays' launch rates allow; the flights going home dock (docks()) - first, as the server resolves them. */
    public static function launches(AIView $view)
    {
        self::$last['launched'] = 0;
        self::$last['craft'] = 0;
        self::$last['held'] = 0;
        $gamedata = $view->gamedata;
        $turn = $view->turn;
        $landing = array(); //hangar id => craft docking there this turn
        $docks = self::docks($view, $landing);
        $out = array();
        foreach ($view->myUnits() as $carrier) {
            if ($carrier instanceof FighterFlight) continue;
            $hangars = HangarOps::collectHangars($carrier);
            if (count($hangars) == 0) continue;
            $cid = (int)$carrier->id;
            $entries = self::dockedHere($carrier, $hangars, $gamedata);
            if (count($entries) == 0 && !isset($docks[$cid])) continue;
            $orders = array(); //hangar id => launch orders
            if (Movement::isPivoting($carrier, $turn, $gamedata) || Movement::isRolling($carrier, $turn, $gamedata)) {
                self::$last['held'] += count($entries);
                $entries = array();
            }

            $budget = array(); //hangar id => craft it may still launch this turn (confirm.js baseBudget), less the craft landing there first
            foreach ($hangars as $h) $budget[(int)$h->id] = max(0, (int)$h->output - (int)$h->launchedThisTurn - (int)$h->landedThisTurn - ($landing[(int)$h->id] ?? 0));

            foreach ($entries as $docked) {
                $entry = $docked['entry'];
                $have = (int)($entry['flightSize'] ?? 0);
                $bays = self::bays($entry, $docked['hangar'], $carrier);
                $size = 0;
                $charge = array();
                for ($k = $have; $k >= 1; $k--) {
                    $c = self::charge($bays, $k, HangarOps::boxesPerCraftForEntry($entry));
                    if ($c === null) continue;
                    $ok = true;
                    foreach ($c as $hid => $craft) if ($craft > $budget[$hid]) $ok = false;
                    if (!$ok) continue;
                    $size = $k;
                    $charge = $c;
                    break;
                }
                if ($size <= 0) {
                    self::$last['held']++;
                    continue;
                }
                foreach ($charge as $hid => $craft) $budget[$hid] -= $craft;
                $orders[(int)$docked['hangar']->id][] = array('phpclass' => (string)$entry['phpclass'], 'size' => $size, 'dockedFlightId' => (int)$entry['dockedFlightId']);
                self::$last['launched']++;
                self::$last['craft'] += $size;
                if ($size < $have) self::$last['held']++;
            }
            //one payload a bay: its launches, and (stage T9, M11) the flights docking in it - the page's Stage 5 shape
            foreach (array_unique(array_merge(array_keys($orders), array_keys($docks[$cid] ?? array()))) as $hid) {
                $payload = array();
                if (isset($orders[$hid])) $payload['launches'] = $orders[$hid];
                if (isset($docks[$cid][$hid])) $payload['docks'] = $docks[$cid][$hid];
                $out[$cid][$hid] = array('id' => $hid, 'individualNotesTransfer' => json_encode($payload));
            }
        }
        return $out;
    }

    /* The launchable entries, in bay order: list of array('hangar', 'entry'). Flights that started docked by rule, and (stage T9, M11)
       the bot's own flights home to reload once nothing more would reload in this carrier (reloadDue) - full, or the reserve spent. */
    private static function dockedHere($carrier, array $hangars, TacGamedata $gamedata)
    {
        $out = array();
        foreach ($hangars as $h) {
            if (!empty($h->isCatapult) || !empty($h->isShadowHangar) || !empty($h->isLCVRail) || !is_array($h->hangarUsage)) continue;
            foreach ($h->hangarUsage as $entry) {
                $id = (int)($entry['dockedFlightId'] ?? 0);
                if ($id <= 0 || !empty($entry['cannotLaunch']) || (int)($entry['flightSize'] ?? 0) <= 0) continue;
                $flight = $gamedata->getShipById($id);
                if (!self::startsDocked($flight) && !self::reloaded($flight, $carrier)) continue;
                $out[] = array('hangar' => $h, 'entry' => $entry);
            }
        }
        return $out;
    }

    // ------------------------------------------------------------------ stage T9, M11: home to reload

    /* Where $flight goes home to: array('carrier', 'hangar', 'hex' - where the carrier will be at the Fire phase: its end when it has
       moved this turn, else straight on), the nearest carrier that can take it whole and would reload it - or null: it is not going home
       (it can still fight, it is not a flight of the bot's own to bring home, or no carrier can take it or reload it). */
    public static function home(AIView $view, $flight)
    {
        if (!self::mayGoHome($view, $flight)) return null;
        $best = null;
        $from = $flight->getHexPos();
        foreach (self::homeBays($view, $flight) as $bay) {
            $d = $from->distanceTo($bay['carrier']->getHexPos());
            if ($best === null || $d < $best['d'] || ($d == $best['d'] && $bay['carrier']->id < $best['carrier']->id)) $best = $bay + array('d' => $d);
        }
        if ($best === null) return null;
        $carrier = $best['carrier'];
        $hex = $carrier->getHexPos();
        if ($carrier->getLastTurnMoved() < $view->turn) {
            $guess = AIView::straightOn($carrier, $view->turn); //(null standing still)
            if ($guess !== null) $hex = $guess->position;
        }
        return array('carrier' => $carrier, 'hangar' => $best['hangar'], 'hex' => $hex);
    }

    /* A flight of the bot's own, on the board, with no usable gun left (Posture::usableGuns - spent gatlings, empty racks): not a
       flight the server flies or one designed to ram (a Hunter-Killer's ram is no gun, and it never reloads), nor a mine. */
    private static function mayGoHome(AIView $view, $flight)
    {
        if (!($flight instanceof FighterFlight) || !empty($flight->remoteControl) || !empty($flight->mine) || self::startsDocked($flight)) return false;
        if ((int)$flight->userid !== (int)$view->botId || !$view->onBoard($flight)) return false;
        if (count(Posture::flightRams($view, $flight)) > 0) return false;
        return count(Posture::usableGuns($view, $flight)) == 0;
    }

    /* The bays of the bot's carriers $flight could go home to: list of array('carrier', 'hangar', 'fit') - ONE bay holding the whole
       flight (one dock order; a catapult - a rear approach, one craft - is left out), whose landing rate takes it in one turn, in a
       carrier that would reload it. $reserved: hangar id => boxes promised to flights docking before it this turn. */
    private static function homeBays(AIView $view, $flight, array $reserved = array())
    {
        $turn = (int)$view->turn;
        $n = $flight->countActiveCraft($turn);
        $out = array();
        foreach ($view->myUnits() as $carrier) {
            if ($carrier instanceof FighterFlight || count(HangarOps::collectHangars($carrier)) == 0) continue;
            if ((int)$carrier->slot !== (int)$flight->slot || !self::reloadDue($flight, $carrier)) continue;
            $fit = self::fit($carrier, $flight, $reserved, array(), $turn);
            if ($fit === null || count($fit['boxes']) != 1 || !empty($fit['hangar']->isCatapult) || (int)$fit['hangar']->output < $n) continue;
            $out[] = array('carrier' => $carrier, 'hangar' => $fit['hangar'], 'fit' => $fit);
        }
        return $out;
    }

    /* The dock orders for this Fire phase: carrier id => hangar id => list of {flightId, count}; $landing (hangar id => craft) is what
       each bay lands - launches() takes it off the bay's rate (Hangar::criticalPhaseEffects: dock, then servicing, then launch). A
       flight going home docks in the first of its bays the server's own test takes it in (HangarOps::canShipReceive: the carrier did
       not pivot or roll, the same hex and heading, the speed within the flight's thrust, the room, the rate) - with the flights already
       docking there counted (boxes and rate). The speed is asked first: past the thrust only a Redline Pilot's d20 could hold. */
    private static function docks(AIView $view, array &$landing)
    {
        self::$last['docking'] = 0;
        $gamedata = $view->gamedata;
        $turn = (int)$view->turn;
        $reserved = array();
        $out = array();
        foreach ($view->myUnits() as $flight) {
            if (!self::mayGoHome($view, $flight)) continue;
            $n = $flight->countActiveCraft($turn);
            $move = $flight->getLastMovement();
            if ($n <= 0 || !$move) continue;
            $thrust = (int)$flight->getEffectiveFreeThrust($turn);
            foreach (self::homeBays($view, $flight, $reserved) as $bay) {
                $carrier = $bay['carrier'];
                $hangar = $bay['hangar'];
                $hid = (int)$hangar->id;
                $end = $carrier->getLastMovement();
                if (!$end || abs((int)$end->speed - (int)$move->speed) > $thrust) continue;
                if ((int)$hangar->launchedThisTurn + (int)$hangar->landedThisTurn + ($landing[$hid] ?? 0) + $n > (int)$hangar->output) continue;
                if (!HangarOps::canShipReceive($hangar, $carrier, $flight, $n, $gamedata)) continue;
                $out[(int)$carrier->id][$hid][] = array('flightId' => (int)$flight->id, 'count' => $n);
                $landing[$hid] = ($landing[$hid] ?? 0) + $n;
                foreach ($bay['fit']['boxes'] as $id => $boxes) $reserved[$id] = ($reserved[$id] ?? 0) + $boxes;
                self::$last['docking']++;
                break;
            }
        }
        return $out;
    }

    /* Would the carrier's servicing still add a round to $flight docked in $carrier (HangarOps::serviceDockedFlights -> each system's
       whileDocked)? A gun that reloads by itself (Weapon::$reloadable - the gatlings) below that cap plus the flight's EXT_AMMO; an old
       missile rack (FighterMissileRack) below its maxAmount, or a magazine below a load the flight bought (AmmoMagazine), while the
       carrier's Ordnance Reserve (HANG_ORD) pays for the round. Every live craft. */
    public static function reloadDue($flight, $carrier)
    {
        $pool = HangarOps::reloadPoolRemaining($carrier);
        $bonus = 0;
        foreach ((array)$flight->enhancementOptions as $option) {
            if (($option[0] ?? '') === 'EXT_AMMO') $bonus += max(0, (int)($option[2] ?? 0));
        }
        foreach ($flight->systems as $craft) {
            if (!($craft instanceof Fighter) || $craft->isDestroyed()) continue;
            foreach ($craft->systems as $system) {
                if ($system->isDestroyed()) continue;
                if ($system instanceof FighterMissileRack) {
                    foreach ((array)$system->missileArray as $round) {
                        if ($round && (int)$round->amount < (int)$system->maxAmount && $pool >= (int)$round->cost) return true;
                    }
                } else if ($system instanceof AmmoMagazine) {
                    foreach ((array)$flight->enhancementOptions as $option) {
                        $class = self::MAGAZINE_ROUNDS[(string)($option[0] ?? '')] ?? null;
                        if ($class === null || (int)($option[2] ?? 0) <= 0 || !class_exists($class)) continue;
                        $round = new $class();
                        if (!array_key_exists($round->modeName, $system->ammoCountArray)) continue;
                        if ((int)$system->ammoCountArray[$round->modeName] < (int)$option[2] && $pool >= (int)$round->getPrice($flight)) return true;
                    }
                } else if ($system instanceof Weapon) {
                    $cap = self::reloadable($system);
                    if ($cap > 0 && isset($system->ammunition) && (int)$system->ammunition < $cap + $bonus) return true;
                }
            }
        }
        return false;
    }

    //A docked flight of the bot's own (not one that starts docked) with nothing more to reload in $carrier: it may go.
    private static function reloaded($flight, $carrier)
    {
        return $flight instanceof FighterFlight && empty($flight->remoteControl) && (int)$flight->userid === (int)$carrier->userid
            && !self::reloadDue($flight, $carrier);
    }

    //Weapon::$reloadable (protected - read as whileDocked reads it): the rounds a docked gun reloads to, 0 for none.
    private static function reloadable(Weapon $weapon)
    {
        return (int)(function () { return $this->reloadable; })->call($weapon);
    }

    //The bays a docked entry is drawn from: its occupancy (HangarOps::occupancyBaysFor), or the bay holding it.
    private static function bays(array $entry, $host, $carrier)
    {
        $bays = HangarOps::occupancyBaysFor($entry, $carrier);
        if (count($bays) == 0) $bays = array(array('hangar' => $host, 'boxes' => HangarOps::boxesForEntry($entry)));
        return $bays;
    }

    /* Craft drawn from each bay to launch $k (the server's distributeCraftAcrossBays and the page's distributeDockedCharge: smallest
       bay first, each up to the craft its boxes hold): hangar id => craft, or null when the bays do not hold $k. */
    private static function charge(array $bays, $k, $bpc)
    {
        $bpc = ((float)$bpc > 0) ? (float)$bpc : 1.0;
        usort($bays, function ($a, $b) { return (int)$a['boxes'] - (int)$b['boxes']; });
        $out = array();
        $left = (int)$k;
        foreach ($bays as $b) {
            if ($left <= 0) break;
            $take = min($left, (int)floor((int)$b['boxes'] / $bpc));
            if ($take <= 0) continue;
            $hid = (int)$b['hangar']->id;
            $out[$hid] = (isset($out[$hid]) ? $out[$hid] : 0) + $take;
            $left -= $take;
        }
        return ($left > 0) ? null : $out;
    }

    //One line for tac_ai_log about the last dockPlan(), or null when nothing had to start docked.
    public static function describeDocks()
    {
        $l = self::$last;
        if ($l['docked'] == 0 && $l['onMap'] == 0) return null;
        return 'hangars: ' . $l['docked'] . ' flights start docked, in ' . $l['carriers'] . ' carriers' . ($l['onMap'] > 0 ? '; ' . $l['onMap'] . ' with no room, on the map' : '');
    }

    //One line for tac_ai_log, or ''.
    public static function describeLaunches()
    {
        $l = self::$last;
        if ($l['launched'] == 0 && $l['held'] == 0 && $l['docking'] == 0) return '';
        return 'hangars: ' . $l['launched'] . ' flights launching (' . $l['craft'] . ' craft)' . ($l['held'] > 0 ? ', ' . $l['held'] . ' waiting for launch room' : '')
            . ($l['docking'] > 0 ? ', ' . $l['docking'] . ' docking to reload' : '');
    }
}
