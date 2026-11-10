<?php

/* Create Game terrain: DRIFTING asteroids and meteor swarms - the "Drifts" tick box beside each of those two rows.

   rules key `terrainDrift`: {asteroids: 1, meteors: 1}, only the kinds ticked - absent in every other game. It covers
   EVERY unit of that kind in the game (user ruling 2026-11-08): placed at random, pre-placed by a map template, or
   bought from the Terrain faction and placed by hand - one-hex asteroids only, never the 2- and 3-hex rocks. The Triad
   Asteroid Salvo's spawns are not asteroids or swarms for this (TerrainDrift::getKind).

   What a drift is - rolled at the start of every turn from turn 2, one hex, the heading veering -60 / 0 / +60, or no
   drift at all - lives in handlers/TerrainDrift.php; a unit the drift carries a field onto is struck in Pre-Firing
   (RammingAttack::getDriftOverruns). */
class TerrainDriftRule implements JsonSerializable {

    const KINDS = array('asteroids', 'meteors');

    private $kinds = array();

    function __construct($data) {
        if (is_object($data)) $data = (array)$data;
        if (!is_array($data)) return;
        foreach (self::KINDS as $kind) {
            if (!empty($data[$kind])) $this->kinds[$kind] = 1;
        }
    }

    public function getRuleName() {
        return 'terrainDrift';
    }

    //Do units of this kind ('asteroids' / 'meteors') drift in this game?
    public function drifts($kind) {
        return isset($this->kinds[$kind]);
    }

    public function isEmpty() {
        return empty($this->kinds);
    }

    public function jsonSerialize(): mixed {
        return $this->kinds;
    }
}
