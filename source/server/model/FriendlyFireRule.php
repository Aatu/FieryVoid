<?php

class FriendlyFireRule implements JsonSerializable {

    public function getRuleName() {
        return 'friendlyFire';
    }

    public function jsonSerialize(): mixed {
        return 1;
    }
}
