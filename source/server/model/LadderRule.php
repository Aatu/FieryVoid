<?php

class LadderRule implements JsonSerializable {

    private $isEnabled = false;    

    function __construct($isEnabled) {
        $this->isEnabled = $isEnabled;        
    }

    public function getRuleName() {
        return 'ladder';
    }

    public function jsonSerialize(): mixed {
        return $this->isEnabled;
    }

    public function ladder() {
        return $this->isEnabled;
    }


}
