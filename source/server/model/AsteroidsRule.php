<?php

class AsteroidsRule implements JsonSerializable {

    private $numberOfAsteroids = 0;    

    function __construct($numberOfAsteroids) {
        $this->numberOfAsteroids = $numberOfAsteroids;        
    }

    public function getRuleName() {
        return 'asteroids';
    }
/*
    public function getAsteroidCount() {
        return $this->numberOfAsteroids;
    }
*/
    public function jsonSerialize(): mixed {
        return $this->numberOfAsteroids;
    }

}

