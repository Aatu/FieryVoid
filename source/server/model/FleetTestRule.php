<?php

class FleetTestRule implements JsonSerializable {

    private $enabled = 0;    

    function __construct($enabled) {
        $this->enabled = $enabled;        
    }

    public function getRuleName() {
        return 'fleetTest';
    }

    public function jsonSerialize(): mixed {
        return $this->enabled;
    }

}
