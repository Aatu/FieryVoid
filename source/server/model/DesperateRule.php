<?php

class DesperateRule implements JsonSerializable {

    private $appliesToTeams = -1;    

    function __construct($appliesToTeams) {
        $this->appliesToTeams = $appliesToTeams;        
    }

    public function getRuleName() {
        return 'desperate';
    }

    public function jsonSerialize(): mixed {
        return $this->appliesToTeams;
    }

}

