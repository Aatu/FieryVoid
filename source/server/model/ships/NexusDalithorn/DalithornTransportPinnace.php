<?php
class DalithornTransportPinnace extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 40*6;
        $this->faction = "Nexus Support Units";
        $this->phpclass = "DalithornTransportPinnace";
        $this->shipClass = "Dalithorn Transport Pinnace";
        $this->imagePath = "img/ships/Nexus/Dalithorn_Pinnace2.png";
		$this->unofficial = true;
	    $this->isd = 2117;
//        $this->canvasSize = 150;

	    $this->notes = '<br>Carries 8 boxes of cargo';

        $this->forwardDefense = 8;
        $this->sideDefense = 10;
        $this->freethrust = 8;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.25;
		
		$this->hangarRequired = 'superheavy'; //for fleet check
        $this->iniativebonus = 70;
    	$this->superheavy = true;
        $this->maxFlightSize = 3;//this is a superheavy fighter originally intended as single unit, limit flight size to 3
	
		$this->populate();

        $this->enhancementOptionsEnabled[] = 'EXT_AMMO'; //To enable extra Ammo for light gun(s).
	
	}

    public function populate(){        

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
		
		for ($i = 0; $i < $toAdd; $i++) {
			$armour = array(4, 3, 3, 3);
			$fighter = new Fighter("DalithornPinnace", $armour, 30, $this->id);
			$fighter->displayName = "Transport Pinnace";
			$fighter->imagePath = "img/ships/Nexus/Dalithorn_Pinnace2.png";
			$fighter->iconPath = "img/ships/Nexus/Dalithorn_Pinnace_Large2.png";

			$light1 = new NexusMinigunFtr(240, 120, 1);
			$fighter->addFrontSystem($light1);
        
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			$this->addSystem($fighter);
		}
    }

//    public function populate(){
//        return;
//    }
    

}

?>
