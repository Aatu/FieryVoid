<?php

class IonBolt extends Weapon{

    public $trailColor = array(30, 170, 255);

    public $name = "ionBolt";
    public $displayName = "Ion Bolt";
    public $animation = "trail";
    public $animationColor = array(127, 0, 255);
    public $animationExplosionScale = 0.15;
    public $projectilespeed = 12;
    public $animationWidth = 3;
    public $trailLength = 20;

    public $loadingtime = 2;
    public $shots = 1;

    public $rangePenalty = 1;
    public $fireControl = array(-4, 0, 0); // fighters, <mediums, <capitals 
    public $exclusive = true;

    function __construct($startArc, $endArc){
        parent::__construct(0, 1, 0, $startArc, $endArc);

    }

    public function getDamage($fireOrder){        return Dice::d(6, 3);  }
    public function setMinDamage(){     $this->minDamage = 3 - $this->dp;      }
    public function setMaxDamage(){     $this->maxDamage = 18 - $this->dp;      }

}