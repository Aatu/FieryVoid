<?php

/* Create Game terrain: the ELECTROMAGNETIC STORM - EM_STORM_PLAN.md.

   "If an EM storm is active, it will affect every unit in the scenario unless otherwise noted." So the rule is
   the whole of it: no hexes and no unit. Each turn's effects are rolled by the EMStorm handler
   (handlers/EMStorm.php) and live on as one-turn criticals.

   rules key `emStorm`: 1. Absent in every other game. Combines with a Nebula, Dust and Mines.

   The effects live at their own sites behind ONE static, TacGamedata::$emStormPresent, which
   TacGamedata::__construct assigns from this rule. The client reads gamedata.rules.emStorm. */
class EMStormRule implements JsonSerializable {

    public function getRuleName() {
        return 'emStorm';
    }

    public function jsonSerialize(): mixed {
        return 1;
    }
}
