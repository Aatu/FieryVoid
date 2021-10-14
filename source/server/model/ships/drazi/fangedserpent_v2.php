<?php
class FangedSerpent_v2 extends SuperHeavyFighter{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 145*6;
        $this->faction = "Drazi";
        $this->phpclass = "FangedSerpent_v2";
        $this->shipClass = "Fanged Serpent Command Fighters";
	    $this->imagePath = "img/ships/drazi/DraziSkyserpent.png";
        $this->occurence = "rare";
	    $this->variantOf = "Sky Serpent Heavy Assault Fighters";
	    $this->isd = 2231;
        $this->canvasSize = 64;

		$this->notes = 'Provides +5 Initiative to all Sky Serpents within 5 hexes.';
        
        $this->forwardDefense = 10;
        $this->sideDefense = 9;
        $this->freethrust = 12;
        $this->offensivebonus = 8;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
		$this->turndelaycost = 0.25;
        
		$this->hangarRequired = 'superheavy'; //for fleet check
	    $this->iniativebonus = 75;
        $this->hasNavigator = true;
        $this->maxFlightSize = 3;//this is a superheavy fighter originally intended as single unit, limit flight size to 3
	
		$this->populate();
	
	}

    public function populate(){        

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
		
		for ($i = 0; $i < $toAdd; $i++) {
			$armour = array(4, 3, 4, 4);
			$fighter = new Fighter("fangedserpent", $armour, 32, $this->id);
			$fighter->displayName = "Fanged Serpent";
			$fighter->imagePath = "img/ships/drazi/DraziSkyserpent.png";
			$fighter->iconPath = "img/ships/drazi/DraziSkyserpent_large.png";

			$fighter->addFrontSystem(new PairedParticleGun(330, 30, 5));
			$fighter->addFrontSystem(new FighterMissileRack(6, 330, 30));
			$fighter->addFrontSystem(new FighterMissileRack(6, 330, 30));
        
			$particleBlaster = new ParticleBlasterFtr(330, 30, 1); //$startArc, $endArc, $nrOfShots
			$fighter->addAftSystem($particleBlaster);
		
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
        
			$this->addSystem($fighter);
		}
    }
    
}
