<?php

/* Create Game: a map template's PRE-PLACED terrain (CREATE_GAME_GAMELOBBY_REDESIGN_PLAN.md
   Stage 2). AsteroidsRule, MoonsRule and DustAndMeteorsRule each ask for a COUNT placed at random;
   this carries every unit's exact hex instead, so a template can offer a balanced map that is the
   same every time it is played.

   rules key `terrainLayout`: {"name": "Asteroid Belt", "units": [{"type","q","r","h"}, ...]}
     type - a key of self::$types. A short name rather than the phpclass: the rules blob is
            published to every client, and the class behind a name is the server's business.
     q, r - the offset hex of the unit's centre.
     h    - facing 0-5. Only the irregular asteroids care (their footprint turns with it); any
            other unit given none is faced at random, as the random pass faces them.

   The layouts themselves live with the templates in createGame.js (mapData[...].terrain). Like a
   deployment zone, a layout is the creator's to choose: it is checked here for SHAPE, not for
   balance or overlaps.

   Both halves of BuyingGamePhase read the list through getUnitsOnMap(), so the unit it creates
   (process, slot 1's submission) and the hex it later puts that unit on (advance) always come from
   the same, bounds-checked list. advance() places these BEFORE the random terrain, which then keeps
   its usual distance from them. */
class TerrainLayoutRule implements JsonSerializable {

    public static $maxUnits = 150;

    //type => phpclass. Every class here is a Terrain unit BuyingGamePhase already knows how to place.
    public static $types = array(
        'asteroidS' => 'asteroidSNew',
        'asteroidM' => 'asteroidMNew',
        'asteroidL' => 'asteroidLNew',
        'asteroid2' => 'asteroidTwoHex',
        'asteroid3' => 'asteroidThreeHex',
        'moonS'     => 'moonSmallNew',
        'moonM'     => 'moonNew',
        'moonL'     => 'moonLarge',
        'dust'      => 'DustField',
        'meteors'   => 'MeteorSwarm',
    );

    private $name;
    private $units = array();

    function __construct($name, $units) {
        $this->name = substr(trim((string)$name), 0, 60);

        if (!is_array($units)) return;
        foreach ($units as $unit) {
            if (is_object($unit)) $unit = (array)$unit;
            if (!is_array($unit) || !isset($unit['type'], $unit['q'], $unit['r'])) continue;
            if (!is_string($unit['type']) || !isset(self::$types[$unit['type']])) continue;

            $clean = array('type' => $unit['type'], 'q' => (int)$unit['q'], 'r' => (int)$unit['r']);
            if (isset($unit['h'])) $clean['h'] = ((int)$unit['h'] % 6 + 6) % 6;
            $this->units[] = $clean;

            if (count($this->units) >= self::$maxUnits) break;
        }
    }

    public function getRuleName() {
        return 'terrainLayout';
    }

    public function isEmpty() {
        return count($this->units) === 0;
    }

    public function getName() {
        return $this->name;
    }

    /* The units whose centre is on the map - the same bounds ReinforcementEntry.onMap and the
       random pass use - each with its phpclass added. $gamespace is
       BuyingGamePhase::getGamespace()'s array, which already turns an unlimited map into 60x40. */
    public function getUnitsOnMap($gamespace) {
        $halfW = (int)floor($gamespace['width'] / 2);
        $halfH = (int)floor($gamespace['height'] / 2);

        $list = array();
        foreach ($this->units as $unit) {
            if (abs($unit['q']) > $halfW || abs($unit['r']) > $halfH) continue;
            $unit['phpclass'] = self::$types[$unit['type']];
            $list[] = $unit;
        }
        return $list;
    }

    public function jsonSerialize(): mixed {
        return array(
            'name' => $this->name,
            'units' => $this->units
        );
    }
}
