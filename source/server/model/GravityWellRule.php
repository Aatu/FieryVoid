<?php

/* Create Game terrain: ONE GRAVITY WELL - GRAVITY_WELL_PLAN.md.

   rules key `gravityWell`: its strength, 1..3. Absent in every other game.

   The rule only says a well exists and how strong it is. BuyingGamePhase::process gives the game one GravityWell unit
   of that strength (userid -5, the random terrain's owner) and BuyingGamePhase::advance places it near the map's
   centre. From then on it is an ordinary unit, exactly like one a player bought from the Terrain faction: the pull
   (GravityWells::resolvePull) and the destruction (GravityWellRammingAttack) both work off the UNIT, never off this rule. */
class GravityWellRule implements JsonSerializable {

    //strength => the unit class carrying it (GRAVITY_WELL_PLAN.md R1: one class per strength)
    public static $classes = array(1 => 'GravityWell', 2 => 'GravityWell2', 3 => 'GravityWell3');

    private $strength = 0;

    function __construct($strength) {
        $strength = (int)$strength;
        $this->strength = isset(self::$classes[$strength]) ? $strength : 0;
    }

    public function getRuleName() {
        return 'gravityWell';
    }

    public function isEmpty() {
        return $this->strength === 0;
    }

    public function getGravityWellClass() {
        return self::$classes[$this->strength] ?? null;
    }

    public function jsonSerialize(): mixed {
        return $this->strength;
    }
}
