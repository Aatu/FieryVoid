<?php

class MoonsRule implements JsonSerializable {

    private $numberOfMoons = 0;    

    function __construct($numberOfMoons) {
        $this->numberOfMoons = $numberOfMoons;        
    }

    public function getRuleName() {
        return 'moons';
    }

    public function jsonSerialize(): mixed {
        return $this->numberOfMoons;
    }

}

