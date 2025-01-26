<?php

class DesperateRule implements JsonSerializable {


    function __construct($numberOfCategories) {
    }

    public function getRuleName() {
        return 'desperate';
    }

    public function jsonSerialize() {
        return true;
    }

}

