<?php
class RogolonVasturSHF extends SuperHeavyFighter{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 125*6;
        $this->faction = "Small Races";
        $this->phpclass = "RogolonVasturSHF";
        $this->shipClass = "Rogolon Vastur Assault Fighter";
        $this->variantOf = "Rogolon Vostor Assault Fighter";
        $this->imagePath = "img/ships/RogolonVostor.png";
	    $this->unofficial = true;
	
		$this->isd = 2255;
        $this->forwardDefense = 8;
        $this->sideDefense = 10;
        $this->freethrust = 9;
        $this->offensivebonus = 6;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.25;
		$this->hangarRequired = 'superheavy'; //for fleet check
        $this->iniativebonus = 70;
        $this->hasNavigator = true;

        $this->maxFlightSize = 3;//this is a superheavy fighter originally intended as single unit, limit flight size to 3
		
		$this->populate();
	
	}

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
		
		for ($i = 0; $i < $toAdd; $i++) {

			$armour = array(5, 4, 3, 3);
			$fighter = new Fighter("RogolonVasturSHF", $armour, 24, $this->id);
			$fighter->displayName = "Vastur SHF";
			$fighter->imagePath = "img/ships/RogolonVostor.png";
			$fighter->iconPath = "img/ships/RogolonVostor_Large.png";
        
			$gun = new RogolonLtPlasmaGun(330, 30, 6, 2);
			$gun->displayName = 'Imp. Lt. Plasma Gun';
			$fighter->addFrontSystem($gun);
			$fighter->addFrontSystem(new RogolonLtPlasmaCannon(330, 30, 1));
			$fighter->addFrontSystem(new FighterMissileRack(4, 330, 30));        
			$fighter->addFrontSystem(new FighterMissileRack(4, 330, 30));
        
			$gun = new RogolonLtPlasmaGun(150, 210, 6, 1);
			$gun->displayName = 'Imp. Lt. Plasma Gun';
			$fighter->addAftSystem($gun);
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
        
			$this->addSystem($fighter);
		}
    }
}
?>
