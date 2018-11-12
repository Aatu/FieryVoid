<?php

class MovementOrder{

    public $id, $type, $position, $facing, $turn, $value, $target;
    public $requiredThrust = array(0, 0, 0, 0, 0); //0:any, 1:front, 2:rear, 3:left, 4:right;
    public $assignedThrust = array();


    function __construct($id, $type, OffsetCoordinate $position, OffsetCoordinate $target, $facing, $turn, $value = 0, $requiredThrust = null, $assignedThrust = null){
        $this->id = (int)$id;
        $this->position = $position;
        $this->target = $target;
        $this->type = $type;
        $this->facing = (int)$facing;
        $this->turn = (int)$turn;
        $this->value = $value;
        $this->requiredThrust = $requiredThrust;
        $this->assignedThrust = $assignedThrust;
    }

    public function getReqThrustJSON(){
        return json_encode($this->requiredThrust);
        
    }

    public function getAssThrustJSON(){
        return json_encode($this->assignedThrust);
        
    }

    public function setReqThrustJSON($json){
        $this->requiredThrust = json_decode($json, true);
    }

    public function setAssThrustJSON($json){
        $this->assignedThrust = json_decode($json, true);
    }

    public function getCoPos(){
        return mathlib::hexCoToPixel($this->position);
    }

    public function getFacingAngle(){

        $d = $this->facing;
        if ($d == 0){
            return 0;
        }
        if ($d == 1){
            return 60;
        }
        if ($d == 2){
            return 120;
        }
        if ($d == 3){
            return 180;
        }
        if ($d == 4){
            return 240;
        }
        if ($d == 5){
            return 300;
        }
        
        return 0;
    }

}
