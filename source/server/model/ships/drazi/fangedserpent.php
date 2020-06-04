<?php
class FangedSerpent extends SuperHeavyFighter{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 145;
        $this->faction = "Drazi";
        $this->phpclass = "FangedSerpent";
        $this->shipClass = "Fanged Serpent Command Fighter";
	    $this->imagePath = "img/ships/drazi/skyserpent.png";
        $this->occurence = "rare";
	    $this->variantOf = "Sky Serpent Heavy Assault Fighter";
	    $this->isd = 2231;
        $this->canvasSize = 64;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 9;
        $this->freethrust = 12;
        $this->offensivebonus = 8;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
        
		$this->hangarRequired = 'superheavy'; //for fleet check
	    $this->iniativebonus = 75;
        $this->hasNavigator = true;
        
        $armour = array(4, 3, 4, 4);
        $fighter = new Fighter("fangedserpent", $armour, 32, $this->id);
        $fighter->displayName = "Fanged Serpent Command Fighter";
        $fighter->imagePath = "img/ships/drazi/skyserpent.png";
        $fighter->iconPath = "img/ships/drazi/skyserpent_large.png";

        $fighter->addFrontSystem(new PairedParticleGun(330, 30, 5));
        $fighter->addFrontSystem(new FighterMissileRack(6, 330, 30));
        $fighter->addFrontSystem(new FighterMissileRack(6, 330, 30));
        
        $particleBlaster = new ParticleBlasterFtr(330, 30, 1); //$startArc, $endArc, $nrOfShots
        $fighter->addFrontSystem($particleBlaster);
		
		$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
        
        $this->addSystem($fighter);
    }
    
    public function populate(){
        return;
    }
}
