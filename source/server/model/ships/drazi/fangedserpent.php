<?php
class FangedSerpent extends SuperHeavyFighter{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 145;
	$this->faction = "Drazi";
        $this->phpclass = "FangedSerpent";
        $this->shipClass = "Fanged Serpent Command Fighter";
	$this->imagePath = "img/ships/skyserpent.png";
        $this->occurence = "rare";
        
        $this->forwardDefense = 10;
        $this->sideDefense = 9;
        $this->freethrust = 12;
        $this->offensivebonus = 8;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
        
	$this->iniativebonus = 80;
        
        $armour = array(4, 3, 4, 4);
        $fighter = new Fighter("skyserpent", $armour, 32, $this->id);
        $fighter->displayName = "Fanged Serpent Command Fighter";
        $fighter->imagePath = "img/ships/skyserpent.png";
        $fighter->iconPath = "img/ships/skyserpent_large.png";

        $fighter->addFrontSystem(new PairedParticleGun(330, 30, 5));
        $particleBlaster = new ParticleBlaster(0, 0, 0, 330, 30);
        $particleBlaster->fireControl = array(-4, 0, 0);
        $fighter->addFrontSystem($particleBlaster);
        
        $this->addSystem($fighter);
    }
}
