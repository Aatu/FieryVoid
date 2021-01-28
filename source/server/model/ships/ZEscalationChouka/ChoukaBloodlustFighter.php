<?php
class ChoukaBloodlustFighter extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 50*6;
        $this->faction = "ZEscalation Chouka";
        $this->phpclass = "ChoukaBloodlustFighter";
        $this->shipClass = "Bloodlust Assault flight";
        $this->imagePath = "img/ships/EscalationWars/ChoukaBloodlust.png";
 //       $this->customFtrName = "Bloodlust";  //Only used for special hangar needs like T-bolts
		$this->unofficial = true;

		
        $this->isd = 1943;
        
        $this->forwardDefense = 9;
        $this->sideDefense = 7;
        $this->freethrust = 6;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
		$this->turndelay = 0.25;
        
        $this->iniativebonus = 80;
        $this->populate();       

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(3, 1, 1, 1);
            $fighter = new Fighter("ChoukaBloodlustFighter", $armour, 15, $this->id);
            $fighter->displayName = "Bloodlust";
            $fighter->imagePath = "img/ships/EscalationWars/ChoukaBloodlust.png";
            $fighter->iconPath = "img/ships/EscalationWars/ChoukaBloodlust_Large.png";

            $fighter->addFrontSystem(new FighterMissileRack(3, 330, 30));
            $fighter->addFrontSystem(new EWLightLaserBeam(330, 30, 2, 2));
            $fighter->addFrontSystem(new FighterMissileRack(3, 330, 30));
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
