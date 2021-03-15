<?php
class ColonialRaptorAssault_K extends SuperHeavyFighter{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 690;
        $this->faction = "ZPlaytest 12 Colonies of Cobol";
        $this->phpclass = "ColonialRaptorAssault_K";
        $this->shipClass = "Raptor (Assault)";
			$this->variantOf = "Raptor";
			$this->occurence = "common";
        $this->imagePath = "img/ships/BSG/raptor.png";
		$this->unofficial = true;
	    $this->isd = 1948;
        $this->canvasSize = 90;

        $this->forwardDefense = 8;
        $this->sideDefense = 10;
        $this->freethrust = 10;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.25;
		
		$this->hangarRequired = 'superheavy'; //for fleet check
        $this->iniativebonus = 70;
        $this->maxFlightSize = 3;//this is a superheavy fighter originally intended as single unit, limit flight size to 3
	
		$this->populate();
	
	}

    public function populate(){        

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
		
		for ($i = 0; $i < $toAdd; $i++) {
			$armour = array(4, 3, 3, 3);
			$fighter = new Fighter("ColonialRaptorAssault_K", $armour, 30, $this->id);
			$fighter->displayName = "Raptor (Assault)";
			$fighter->imagePath = "img/ships/BSG/raptor.png";
			$fighter->iconPath = "img/ships/BSG/raptor_large.png";


            $missileRack = new FighterMissileRack(4, 330, 30);
            $missileRack->firingModes = array(
                1 => "Y",
				2 => "B"
            );

            $missileRack->missileArray = array(
                1 => new MissileFY(330, 30),
				2 => new MissileFB(330, 30)
            );

            $fighter->addFrontSystem($missileRack);  

//			$light1 = new NexusMinigunFtr(180, 360, 1);
//			$fighter->addFrontSystem($light1);
//	        $autocannon = new NexusAutocannonFtr(300, 60, 1); //$startArc, $endArc, $nrOfShots
//	        $fighter->addFrontSystem($autocannon);
//			$light2 = new NexusMinigunFtr(0, 180, 1);
//			$fighter->addFrontSystem($light2);
        
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			$this->addSystem($fighter);
		}
    }

//    public function populate(){
//        return;
//    }
    

}

?>
