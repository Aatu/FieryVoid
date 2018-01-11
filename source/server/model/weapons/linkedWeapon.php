<?php
class LinkedWeapon extends Weapon{
	public $isLinked = true; //indicates that this is linked weapon, no need for overrides

    function __construct($armour, $maxhealth, $powerReq, $startArc, $endArc){
        parent::__construct($armour, $maxhealth, $powerReq, $startArc, $endArc);
    }

}
