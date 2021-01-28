<?php
class ChoukaBloodfireFighter extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 70*6;
        $this->faction = "ZEscalation Chouka";
        $this->phpclass = "ChoukaBloodfireFighter";
        $this->shipClass = "Bloodfire Suicide flight";
			$this->variantOf = "Bloodlust Assault flight";
			$this->occurence = "rare";
        $this->imagePath = "img/ships/EscalationWars/ChoukaBloodlust.png";
 //       $this->customFtrName = "Bloodlust";  //Only used for special hangar needs like T-bolts
		$this->unofficial = true;

	    $this->occurence = 'special';
	    $this->notes = 'Special deployment: 1 in 9 fighers.';//let's try this way...
	    $this->notes .= '<br>Allowed to ram and can score 60 damage.';
		
        $this->isd = 1973;
        
        $this->forwardDefense = 9;
        $this->sideDefense = 7;
        $this->freethrust = 8;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
		$this->turndelay = 0;
        
        $this->iniativebonus = 80;
        $this->populate();       

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(2, 1, 1, 1);
            $fighter = new Fighter("ChoukaBloodfireFighter", $armour, 15, $this->id);
            $fighter->displayName = "Bloodfire";
            $fighter->imagePath = "img/ships/EscalationWars/ChoukaBloodlust.png";
            $fighter->iconPath = "img/ships/EscalationWars/ChoukaBloodlust_Large.png";

			$hitPenalty = 1; // This is actually a +1 bonus to ramming
			$ram = new RammingAttack(0, 0, 360, 60, $hitPenalty, true, 60);
            $fighter->addFrontSystem(new EWLightLaserBeam(330, 30, 2, 2));
			$fighter->addAftSystem($ram);
//			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
