<?php
class ChoukaBloodhoundFighter extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 50*6;
        $this->faction = "Escalation Wars Chouka Theocracy";
        $this->phpclass = "ChoukaBloodhoundFighter";
        $this->shipClass = "Bloodhound Anti-Fighter flight";
			$this->variantOf = "Bloodlust Assault flight";
			$this->occurence = "uncommon";
        $this->imagePath = "img/ships/EscalationWars/ChoukaBloodlust.png";
 //       $this->customFtrName = "Bloodlust";  //Only used for special hangar needs like T-bolts
		$this->unofficial = true;

        $this->isd = 1971;
        
        $this->forwardDefense = 9;
        $this->sideDefense = 7;
        $this->freethrust = 7;
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
            $armour = array(2, 1, 2, 2);
            $fighter = new Fighter("ChoukaBloodlustFighter", $armour, 15, $this->id);
            $fighter->displayName = "Bloodhound";
            $fighter->imagePath = "img/ships/EscalationWars/ChoukaBloodlust.png";
            $fighter->iconPath = "img/ships/EscalationWars/ChoukaBloodlust_Large.png";

            $missileRack1 = new FighterMissileRack(4, 330, 30);
            $missileRack1->firingModes = array(
                1 => "FY"
            );

            $missileRack1->missileArray = array(
                1 => new MissileFY(330, 30)
            );

            $missileRack2 = new FighterMissileRack(4, 330, 30);
            $missileRack2->firingModes = array(
                1 => "FY"
            );

            $missileRack2->missileArray = array(
                1 => new MissileFY(330, 30)
            );

            $fighter->addFrontSystem($missileRack1);
            $fighter->addFrontSystem(new EWLightLaserBeam(330, 30, 2, 2));
            $fighter->addFrontSystem($missileRack2);

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
