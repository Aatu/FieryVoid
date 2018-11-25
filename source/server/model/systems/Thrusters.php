<?php

class Thruster extends ShipSystem
{
    public $name = "thruster";
    public $thruster = true;
    public $displayName = "Thruster";
    public $direction;
    public $isPrimaryTargetable = true; //can this system be targeted by called shot if it's on PRIMARY?

    public $possibleCriticals = array(15 => "FirstThrustIgnored", 20 => "HalfEfficiency", 25 => array("FirstThrustIgnored", "HalfEfficiency"));

    public function __construct($armour, $maxhealth, $powerReq, $output, $direction)
    {
        parent::__construct($armour, $maxhealth, $powerReq, $output);

        if (is_array($direction)) {
            $this->direction = $direction;
        } else {
            $this->direction = [$direction];
        }

    }

    public function canChannelAmount($amount)
    {
        if ($this->hasCritical("FirstThrustIgnored") || $this->hasCritical("HalfEfficiency")) {
            return $amount <= $this->output;
        } else {
            return $amount <= $this->output * 2;
        }
    }

    public function getChannelCost($amount)
    {
        $cost = 0;

        if ($this->hasCritical("FirstThrustIgnored")) {
            $cost += 1;
        }

        if ($this->hasCritical("HalfEfficiency")) {
            $cost += $amount * 2;
        } else {
            $cost += $amount;
        }

        return $cost;
    }

    public function getOverChannel($amount)
    {
        if ($this->hasCritical("FirstThrustIgnored") || $this->hasCritical("HalfEfficiency")) {
            return 0;
        } else if ($amount < $this->output) {
            return 0;
        } else {
            return $amount - $this->output;
        }
    }

    public function isDirection($direction)
    {
        return in_array($direction, $this->direction);
    }

} //endof Thruster

class ManouveringThruster extends Thruster
{
    public $name = "manouveringThruster";
    public $displayName = "Manouvering thruster";
    public $maxEvasion = 0;

    public function __construct($armour, $maxhealth, $powerReq, $output, $maxEvasion)
    {
        parent::__construct($armour, $maxhealth, $powerReq, $output, 6, 0);

        $this->maxEvasion = $maxEvasion;
    }
}

class InvulnerableThruster extends Thruster
{
    /*sometimes thruster is techically necessary, despite the fact that it shouldn't be there (eg. on LCVs)*/
    /*this thruster will be almost impossible to damage :) (it should be out of hit table, too!)*/
    public $isPrimaryTargetable = false; //can this system be targeted by called shot if it's on PRIMARY?

    public function __construct($armour, $maxhealth, $powerReq, $output, $direction, $thrustused = 0)
    {
        parent::__construct($armour, $maxhealth, $powerReq, $output, $direction, $thrustused);
    }

    public function getArmourInvulnerable($target, $shooter, $dmgClass, $pos = null)
    { //this thruster should be invulnerable to anything...
        $activeAA = 99;
        return $activeAA;
    }

    public function testCritical($ship, $add = 0)
    { //this thruster won't suffer criticals ;)
        return [];
    }
} //endof InvulnerableThruster
