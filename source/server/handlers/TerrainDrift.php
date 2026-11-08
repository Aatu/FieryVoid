<?php

/* DRIFTING ASTEROIDS AND METEOR SWARMS - Create Game's "Drifts" tick boxes (TerrainDriftRule).

   User rulings 2026-11-08:
   - At the START of every turn from turn 2, each drifting unit rolls: 25% it stays where it is; otherwise it moves ONE
     hex - 25% each straight on along its heading, or veering 60 degrees to either side - and its heading becomes the
     direction it moved. Its facing never changes.
   - No checks: it moves as rolled, onto other terrain or off the map.
   - A unit sitting in a hex the field drifts into is struck in Pre-Firing as though it had moved into the field, on
     the side facing the drift (RammingAttack::getDriftOverruns).
   - Every ONE-HEX asteroid and every meteor swarm in the game drifts, however it got onto the map (getKind); the 2- and
     3-hex asteroids do not.

   One movement row per drifting unit per turn, type 'drift', dated the new turn, written by resolve() from
   Manager::changeTurn - so the turn's Deployment and Initial Orders already show the field where it now is. A drift
   is one hex at speed 1; staying put is a row in the SAME hex at speed 0.
   ⚠️ THE STAY ROW IS LOAD-BEARING. The movement loader (DBManager, the tac_shipmovement read) fetches only turn 1, the
   current and previous turns, and every 'start' / 'deploy' row - ships are safe because every ship has a row every turn.
   A field that drifted and then sat still for two turns would load at the hex it was PLACED on.
   A 'drift' row is never a hex transit (RammingAttack::isHexTransit reads only units' own rows). The replay plays every
   drift of the turn at once, before the turn's movement (ReplayAnimationStrategy animateTerrainDrift); a drifting unit's
   icon wears the heading arrow (gamedata.isDriftingTerrain). */
class TerrainDrift {

    const MOVE_TYPE = 'drift';

    //'asteroids' / 'meteors' for a unit under that kind's tick box, null for anything else - the Triad Asteroid
    //Salvo's spawns included. ONE-HEX asteroids only (user, 2026-11-08): the 2- and 3-hex rocks are rammed at full
    //ramming damage, like moons, and never drift. The client's mirror is gamedata.DRIFT_KINDS.
    public static function getKind($ship){
        if ($ship instanceof asteroidSNew || $ship instanceof asteroidMNew || $ship instanceof asteroidLNew) return 'asteroids';
        if ($ship instanceof MeteorSwarm) return 'meteors';
        return null;
    }

    /* Manager::changeTurn, on the new turn's freshly loaded gamedata: roll every drifting unit's drift. */
    public static function resolve($gamedata, $dbManager){
        $rule = $gamedata->rules ? $gamedata->rules->getRuleByName('terrainDrift') : null;
        if (!$rule) return;
        if ($gamedata->turn < 2) return; //turn 1 starts with the terrain just placed

        foreach ($gamedata->ships as $unit) {
            $kind = self::getKind($unit);
            if ($kind === null || !$rule->drifts($kind)) continue;
            if ($unit->isDestroyed()) continue;
            if ($unit->getTurnDeployed($gamedata) >= $gamedata->turn) continue; //not on the map before this turn
            if (self::getDriftOnTurn($unit, $gamedata->turn) !== null) continue; //rolled once a turn, never again
            $last = $unit->getLastMovement();
            $from = $last ? Movement::toOffset($last->position) : null;
            if (!$from) continue;

            $roll = Dice::d(4);
            if ($roll == 1) { //stays put, heading and all - still a row (see the header)
                $row = new MovementOrder(null, self::MOVE_TYPE, $from, 0, 0, 0, (int)$last->heading, (int)$last->facing,
                    false, $gamedata->turn, 0, 0);
            } else {
                $heading = Mathlib::addToHexFacing((int)$last->heading, $roll - 3); //2: veers left, 3: straight on, 4: veers right
                $row = new MovementOrder(null, self::MOVE_TYPE, $from->moveToDirection($heading), 0, 0, 1, $heading, (int)$last->facing,
                    false, $gamedata->turn, 0, 0);
            }
            $dbManager->submitMovement($gamedata->id, $unit->id, $gamedata->turn, array($row));
            $unit->movement[] = $row;
        }
    }

    /* The unit's drift row on $turn: array('from' => the row before it - where the field stood - , 'to' => the drift
       row), or null when it has none (not a drifting unit, or not rolled yet). A stay has 'from' and 'to' in one hex. */
    public static function getDriftOnTurn($unit, $turn){
        $previous = null;
        foreach ($unit->movement as $move) {
            if ($move->type === self::MOVE_TYPE && $move->turn == $turn) return array('from' => $previous, 'to' => $move);
            $previous = $move;
        }
        return null;
    }
}
