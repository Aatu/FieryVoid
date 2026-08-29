<?php

/* REINFORCEMENTS_PLAN.md §2.1 — "Allow Reinforcements", set at Create Game.
   Off means nothing in that document exists: the lobby sells no reinforcements, so no unit is
   ever flagged, so no jump point exit is ever declared. A pure on/off, exactly like
   AllowMinesRule, which this mirrors file for file. */
class AllowReinforcementsRule implements JsonSerializable {

    public function getRuleName() {
        return 'allowReinforcements';
    }

    public function jsonSerialize(): mixed {
        return 1;
    }
}
