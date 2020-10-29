<?php
class CircasianJagaFighter extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 36*6;
        $this->faction = "ZEscalation Circasian";
        $this->phpclass = "CircasianJagaFighter";
        $this->shipClass = "Jaga Medium flight";
        $this->imagePath = "img/ships/EscalationWars/CircasianJaga.png";
        $this->customFtrName = "Jaga";
		$this->unofficial = true;
		
		
        $this->isd = 1963;
        
        $this->forwardDefense = 7;
        $this->sideDefense = 7;
        $this->freethrust = 10;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
		$this->turndelay = 0;
        
        $this->iniativebonus = 90;
        $this->populate();       

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(2, 2, 1, 1);
            $fighter = new Fighter("CircasianJagaFighter", $armour, 10, $this->id);
            $fighter->displayName = "Jaga";
            $fighter->imagePath = "img/ships/EscalationWars/CircasianJaga.png";
            $fighter->iconPath = "img/ships/EscalationWars/CircasianJaga_Large.png";

			$gun = new PairedParticleGun(330, 30, 2);
			$gun->displayName = "Paired Particle Beam";
			$fighter->addFrontSystem($gun);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
