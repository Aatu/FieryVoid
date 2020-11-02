<?php
class ChoukaFaithbringerFighter extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 42*6;
        $this->faction = "ZEscalation Chouka";
        $this->phpclass = "ChoukaFaithbringerFighter";
        $this->shipClass = "Faithbringer Heavy flight";
        $this->imagePath = "img/ships/EscalationWars/ChoukaFaithbringer.png";
        $this->customFtrName = "Faithbringer";
		$this->unofficial = true;

		
        $this->isd = 1915;
        
        $this->forwardDefense = 8;
        $this->sideDefense = 6;
        $this->freethrust = 7;
        $this->offensivebonus = 4;
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
            $fighter = new Fighter("ChoukaFaithbringerFighter", $armour, 12, $this->id);
            $fighter->displayName = "Faithbringer";
            $fighter->imagePath = "img/ships/EscalationWars/ChoukaFaithbringer.png";
            $fighter->iconPath = "img/ships/EscalationWars/ChoukaFaithbringer_Large.png";

            $fighter->addFrontSystem(new FighterMissileRack(2, 330, 30));
			$gun = new EWPlasmaGun(330, 30, 6, 1);
			$gun->displayName = "Plasma Gun";
			$fighter->addFrontSystem($gun);
            $fighter->addFrontSystem(new FighterMissileRack(2, 330, 30));
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
