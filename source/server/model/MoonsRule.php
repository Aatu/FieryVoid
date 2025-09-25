<?php
/*
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
*/
class MoonsRule implements JsonSerializable {

    private $small;
    private $medium;
    private $large;

    function __construct($small, $medium, $large) {
        $this->small = (int)$small;
        $this->medium = (int)$medium;
        $this->large = (int)$large;
    }

    public function getRuleName() {
        return 'moons';
    }

    public function jsonSerialize(): mixed {
        return [
            'small' => $this->small,
            'medium' => $this->medium,
            'large' => $this->large
        ];
    }
}
