<?php

/* Create Game terrain: the NEBULA (B5W Kitchen Sink 12.5.7) - NEBULA_PLAN.md.

   "As with dust zones, a nebula fills the entire map." So the rule is the whole of it: no hexes (it is
   everywhere) and no unit (nothing in it does damage, so nothing needs a shooter - unlike the Dust Zone).

   rules key `nebula`: 1. Absent in every other game.

   The effects live at their own sites behind ONE static, TacGamedata::$nebulaPresent, which
   TacGamedata::__construct assigns from this rule - see the Nebula handler (handlers/Nebula.php) for the
   list. The client reads gamedata.rules.nebula.

   ⚠️ Mines are not allowed in a nebula (KS 9.9, user ruling 2026-11-06): GameRules never builds an
   AllowMinesRule beside this one, so the stored rules, the lobby and every later load agree whatever the
   creating POST said. */
class NebulaRule implements JsonSerializable {

    public function getRuleName() {
        return 'nebula';
    }

    public function jsonSerialize(): mixed {
        return 1;
    }
}
