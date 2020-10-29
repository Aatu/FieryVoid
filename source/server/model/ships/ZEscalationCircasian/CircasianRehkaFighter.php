<?php
class CircasianRehkaFighter extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 36*6;
        $this->faction = "ZEscalation Circasian";
        $this->phpclass = "CircasianRehkaFighter";
        $this->shipClass = "Rehka Heavy flight";
        $this->imagePath = "img/ships/EscalationWars/CircasianRehka.png";
        $this->customFtrName = "Rehka";
		$this->unofficial = true;

		
	    $this->notes = 'Non-atmospheric.';
		
        $this->isd = 1950;
        
        $this->forwardDefense = 9;
        $this->sideDefense = 8;
        $this->freethrust = 7;
        $this->offensivebonus = 3;
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
            $fighter = new Fighter("CircasianRehkaFighter", $armour, 11, $this->id);
            $fighter->displayName = "Rehka";
            $fighter->imagePath = "img/ships/EscalationWars/CircasianRehka.png";
            $fighter->iconPath = "img/ships/EscalationWars/CircasianRehka_Large.png";

			$gun = new PairedParticleGun(330, 30, 2);
			$gun->displayName = "Paired Particle Beam";
			$fighter->addFrontSystem($gun);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
