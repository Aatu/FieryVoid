<?php

/* Create Game terrain: Dust and Meteor Swarms (CREATE_GAME_GAMELOBBY_REDESIGN_PLAN.md §3.2,
   Stage 1). Same shape as MoonsRule - one rule carrying one count per terrain type.

   Each count is a number of units, placed at random in BuyingGamePhase:
     dust    -> DustField    ("Dust Field" on the map), one hex - but one in six is a Dust Cloud of
                3, 5 or 7 hexes (DustCloudThreeHex / FiveHex / SevenHex, final refinements §12.12)
     meteors -> MeteorSwarm  ("Meteoroid Swarm" on the map; "Meteor Swarms" in Create Game), one hex
   Both live in ships/terrain and mirror the Triad Asteroid Salvo's spawnDustField / spawnMeteoroid
   (specialWeapons.php), sharing their collision damage and their NOT blocking line of sight.

   Unlike every other terrain type they MAY be placed adjacent to other terrain, moons included
   (user ruling 2026-09-23) - they just never share a hex with it. */
class DustAndMeteorsRule implements JsonSerializable {

    public static $maxDust = 36; //same ceiling as asteroids 
    public static $maxMeteors = 36; //same ceiling as asteroids   

    private $dust;
    private $meteors;

    function __construct($dust, $meteors) {
        $this->dust = max(0, min(self::$maxDust, (int)$dust));
        $this->meteors = max(0, min(self::$maxMeteors, (int)$meteors));
    }

    public function getRuleName() {
        return 'dustAndMeteors';
    }

    public function jsonSerialize(): mixed {
        return [
            'dust' => $this->dust,
            'meteors' => $this->meteors
        ];
    }
}
