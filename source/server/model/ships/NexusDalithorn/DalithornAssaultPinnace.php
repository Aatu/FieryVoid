<?php
class DalithornAssaultPinnace extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 750;
        $this->faction = "Nexus Dalithorn Commonwealth";
        $this->phpclass = "DalithornAssaultPinnace";
        $this->shipClass = "Thenivor Assault Pinnace";
			$this->variantOf = "Arentith Pinnace";
			$this->occurence = "rare";
        $this->imagePath = "img/ships/Nexus/Dalithorn_Pinnace2.png";
		$this->unofficial = true;
	    $this->isd = 2113;
//        $this->canvasSize = 150;

        $this->forwardDefense = 8;
        $this->sideDefense = 10;
        $this->freethrust = 10;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.25;
		
		$this->hangarRequired = 'superheavy'; //for fleet check
        $this->iniativebonus = 70;
		$this->notes = "Bonus to attaching to enemy ships.";	
		$this->notes .= "<br>Common variant on assault and troops ships.";	
    	$this->superheavy = true;
        $this->maxFlightSize = 3;//this is a superheavy fighter originally intended as single unit, limit flight size to 3
	
		$this->populate();
	
	}

    public function populate(){        

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
		
		for ($i = 0; $i < $toAdd; $i++) {
			$armour = array(4, 3, 3, 3);
			$fighter = new Fighter("DalithornAssaultPinnace", $armour, 30, $this->id);
			$fighter->displayName = "Thenivor";
			$fighter->imagePath = "img/ships/Nexus/Dalithorn_Pinnace2.png";
			$fighter->iconPath = "img/ships/Nexus/Dalithorn_Pinnace_Large2.png";

			$light1 = new NexusMinigunFtr(180, 360, 1);
			$fighter->addFrontSystem($light1);

			$fighter->addFrontSystem(new ExtraMarines(0, 360, 0, false)); //startarc, endarc, damagebonus, elite.

			$light2 = new NexusMinigunFtr(0, 180, 1);
			$fighter->addFrontSystem($light2);
        
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			$this->addSystem($fighter);
		}
    }


}

?>
