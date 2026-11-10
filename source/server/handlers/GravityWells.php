<?php

/* THE GRAVITY WELL'S PULL - GRAVITY_WELL_PLAN.md.

   "All units are drawn towards the gravity well's hex when all other movement is complete, a number of hexes equal
   to the strength of the gravity well. The first priority is to move it into the hex closest to the [well]. If there
   is more than one choice, move the unit in the direction of its motion. A unit will not be pulled farther than the
   center hex of the gravity source. Advanced races treat gravity sources as 1 point less than they actually are."

   resolvePull runs ONCE per turn, from MovementGamePhase::advance, after every unit's movement is in and before
   anything reads where they ended up. Per pulled unit it writes:
     - one log order, GravityWellPull (well -> unit), inserted at once for its id;
     - one movement row per hex dragged, type 'gravity', value = that order's id - so the replay animates the drag
       after the log line (ReplayAnimationStrategy pairs movement.value with an incoming order's id), the bearing
       walk-back treats it as a forced shift (BaseShip::getPreviousCoPos), and the collision sweep counts every hex
       of it as entered (RammingAttack::isHexTransit, R2).
   A unit dragged into the well's own hex is destroyed in Pre-Firing by GravityWellRammingAttack.

   THE GATE: getActiveWells is one instanceof per unit. In every game without a well nothing else here runs. */
class GravityWells {

    const PULL_CLASS = 'GravityWellPull';            //the log line's damageclass
    const COLLISION_CLASS = 'GravityWellCollision';  //the destruction's (GravityWell::$terrainCollisionType)
    const MOVE_TYPE = 'gravity';                     //the pull's movement rows (RammingAttack::isHexTransit spells it out)

    //Every well on the board this turn, lowest id first.
    public static function getActiveWells($gamedata){
        $wells = array();
        foreach ($gamedata->ships as $ship) {
            if (!($ship instanceof GravityWell)) continue;
            if ($ship->isDestroyed()) continue;
            if ($ship->getTurnDeployed($gamedata) > $gamedata->turn) continue;
            if (!$ship->getLastMovement()) continue;
            $wells[] = $ship;
        }
        usort($wells, function ($a, $b) { return $a->id - $b->id; });
        return $wells;
    }

    public static function resolvePull($gamedata, $dbManager){
        $wells = self::getActiveWells($gamedata);
        if (empty($wells)) return;

        //A unit in ANY well's hex is pulled no farther, by that well or another.
        $wellHexes = array();
        foreach ($wells as $well) {
            $hex = Movement::toOffset($well->getHexPos());
            if ($hex) $wellHexes[$hex->q . ',' . $hex->r] = true;
        }

        $units = array_values($gamedata->ships);
        usort($units, function ($a, $b) { return $a->id - $b->id; });

        foreach ($wells as $well) {
            $weapon = $well->getSystemByName('GravityWellRammingAttack');
            $wellHex = Movement::toOffset($well->getHexPos());
            if (!$weapon || !$wellHex) continue;

            foreach ($units as $unit) {
                if (self::isUnmoved($unit, $gamedata)) continue;

                $steps = self::getPullStrength($well, $unit);
                if ($steps <= 0) continue;

                $lastMove = $unit->getLastMovement();
                $from = $lastMove ? Movement::toOffset($lastMove->position) : null;
                if (!$from) continue;

                $path = self::getPullPath($from, $wellHex, $lastMove->heading, $steps, $wellHexes);
                if (empty($path)) continue;

                self::applyPull($unit, $well, $weapon, $path, $gamedata, $dbManager, null);

                //A rider (breaching pod, grappling ship) goes where its host goes.
                if (!empty($unit->hasAttached)) {
                    foreach (array_keys($unit->hasAttached) as $riderId) {
                        $rider = $gamedata->getShipById((int)$riderId);
                        if (!$rider || $rider->isDestroyed() || !isset($rider->attached[$unit->id])) continue;
                        self::applyPull($rider, $well, $weapon, $path, $gamedata, $dbManager, $unit);
                    }
                }
            }
        }
    }

    /* Not pulled at all: terrain (asteroids, moons, other wells - every terrain unit is a base too), bases, OSATs and
       mines, anything gone or not yet on the board - and a rider, which is moved with its host instead. */
    public static function isUnmoved($unit, $gamedata){
        if ($unit->isDestroyed()) return true;
        if ($unit->isTerrain()) return true;
        if ($unit->base || $unit->smallBase || !empty($unit->osat) || $unit->mine) return true;
        if ($unit->getTurnDeployed($gamedata) > $gamedata->turn) return true;
        if (!empty($unit->attached)) return true;
        return false;
    }

    //"Advanced races treat gravity sources as 1 point less than they actually are" - factionAge 3+, as the Nebula and EM Storm read it.
    public static function getPullStrength($well, $unit){
        $strength = (int)$well->gravityStrength;
        if ((int)$unit->factionAge >= 3) $strength--;
        return max(0, $strength);
    }

    //The hexes a unit at $from is dragged through, in order: $steps of them, or fewer if it reaches a well.
    public static function getPullPath(OffsetCoordinate $from, OffsetCoordinate $wellHex, $heading, $steps, $wellHexes){
        $path = array();
        $pos = $from;
        for ($i = 0; $i < $steps; $i++) {
            if (isset($wellHexes[$pos->q . ',' . $pos->r])) break; //"not pulled farther than the center hex"
            $pos = self::getPullStep($pos, $wellHex, $heading);
            if ($pos === null) break;
            $path[] = $pos;
        }
        return $path;
    }

    /* One hex nearer the well. Off its six hex lines two neighbours are equally near, and the one nearer the unit's
       heading is taken (R3). Those two are always ADJACENT directions, so against a whole-number heading they can
       never be equally near it either - the rule's "last appropriate direction" fallback is never needed. */
    public static function getPullStep(OffsetCoordinate $from, OffsetCoordinate $wellHex, $heading){
        $distance = $from->distanceTo($wellHex);
        if ($distance <= 0) return null;
        $heading = (((int)$heading % 6) + 6) % 6;

        $best = null;
        $bestTurn = 99;
        for ($dir = 0; $dir < 6; $dir++) {
            $hex = $from->moveToDirection($dir);
            if ($hex->distanceTo($wellHex) != $distance - 1) continue;
            $turn = abs($dir - $heading);
            $turn = min($turn, 6 - $turn);
            if ($turn < $bestTurn) {
                $best = $hex;
                $bestTurn = $turn;
            }
        }
        return $best;
    }

    /* The log order first (its id goes on every row), then the rows - into the database and onto the unit in memory,
       so whatever the Movement advance reads after this (ELINT arcs, missile detection) sees the pulled position. */
    private static function applyPull($unit, $well, $weapon, $path, $gamedata, $dbManager, $host){
        $count = count($path);
        $end = $path[$count - 1];
        $wellHex = Movement::toOffset($well->getHexPos());
        $intoWell = ($end->q == $wellHex->q && $end->r == $wellHex->r);
        $hexes = $count . ' ' . ($count == 1 ? 'hex' : 'hexes');

        /* rolled 1 marks it resolved (the printed log drops a rolled-0 order, and Firing::fire skips a rolled one).
           shots and shotshit stay 0: DBManager::submitDamages hangs damage with no order id on this weapon's order
           at the same target with shotshit > 0 - which must only ever be the collision.
           x/y stay 0: the rows carry the hexes, and an order's x/y is not masked - for a stealthed unit it would
           hand its position to every enemy (hideStealthShipMovement masks the rows themselves). */
        $order = new FireOrder(-1, 'prefiring', $well->id, $unit->id, $weapon->id, -1, (int)$gamedata->turn, 1,
            0, 1, 0, 0, 0, 0, 0, self::PULL_CLASS, -1);
        if ($host) {
            $order->pubnotes = self::shipLink($unit) . ' is carried ' . $hexes . ' with ' . self::shipLink($host)
                . ($intoWell ? ' into the gravity well!' : ' towards the gravity well.');
        } else {
            $order->pubnotes = self::shipLink($unit) . ' is drawn ' . $hexes
                . ($intoWell ? ' into the gravity well!' : ' towards the gravity well.');
        }
        $order->id = (int)$dbManager->submitSingleFireorder($gamedata->id, $order);
        $order->addToDB = false;
        $weapon->fireOrders[] = $order;

        $last = $unit->getLastMovement();
        $rows = array();
        foreach ($path as $hex) {
            $rows[] = new MovementOrder(-1, self::MOVE_TYPE, new OffsetCoordinate($hex->q, $hex->r), 0, 0,
                $last->speed, $last->heading, $last->facing, false, (int)$gamedata->turn, $order->id, 0);
        }
        $dbManager->insertMovement($gamedata->id, $unit->id, $rows);
        foreach ($rows as $row) $unit->setMovement($row);
    }

    /* Destroyed outright - a ship's primary structure, or every craft of a flight (which has no primary structure).
       Each entry names this weapon, so DBManager::submitDamages finds the collision order it belongs to. */
    public static function destroyOutright($target, $weapon, $fireOrder, $gamedata){
        $shooterId = $weapon->getUnit()->id;
        $systems = array();
        if ($target instanceof FighterFlight) {
            foreach ($target->systems as $craft) {
                if (!$craft->isDestroyed($gamedata->turn)) $systems[] = $craft;
            }
        } else {
            $structure = $target->getStructureSystem(0);
            if ($structure && !$structure->isDestroyed()) $systems[] = $structure;
        }

        foreach ($systems as $system) {
            $entry = new DamageEntry(-1, $target->id, $gamedata->id, $gamedata->turn, $system->id,
                $system->getRemainingHealth(), 0, 0, $fireOrder->id, true, false, "", "Standard", $shooterId, $weapon->id);
            $entry->updated = true;
            $system->damage[] = $entry;
        }
    }

    //A unit's name for a log line. Bare: the client colours it for whoever is reading (combatLog.colourShipLinksInNotes).
    public static function shipLink($unit){
        return '<span class="shiplink" data-id="' . (int)$unit->id . '">' . $unit->name . '</span>';
    }
}
