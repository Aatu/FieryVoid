<?php
class VelraxCasrissBFighter extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 24*6;
        $this->faction = "ZPlaytest Velrax";
        $this->phpclass = "VelraxCasrissBFighter";
        $this->shipClass = "Casriss-B flight";
			$this->variantOf = "Casriss-A flight";
			$this->occurence = "common";
        $this->imagePath = "img/ships/Playtest/VelraxCasriss.png";
		$this->unofficial = true;

        $this->isd = 2059;
        
        $this->forwardDefense = 5;
        $this->sideDefense = 6;
        $this->freethrust = 9;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 10;
        $this->turncost = 0.33;
		$this->turndelay = 0;
        
        $this->iniativebonus = 100;
        $this->populate();       

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(1, 1, 0, 0);
            $fighter = new Fighter("VelraxCasrissAFighter", $armour, 7, $this->id);
            $fighter->displayName = "Casriss-A";
            $fighter->imagePath = "img/ships/Playtest/VelraxCasriss.png";
            $fighter->iconPath = "img/ships/Playtest/VelraxCasriss_Large.png";

	        $light = new NexusLightIonGun(330, 30, 0); //$startArc, $endArc, $nrOfShots
	        $fighter->addFrontSystem($light);
	        $fighter->addFrontSystem($light);
            	$missileRack = new FighterMissileRack(1, 330, 30);
            	$missileRack->firingModes = array( 1 => "FY" );
            	$missileRack->missileArray = array( 1 => new MissileFY(330, 30) );
			$fighter->addFrontSystem($missileRack);	

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
