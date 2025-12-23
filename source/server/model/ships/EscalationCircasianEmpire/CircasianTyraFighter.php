<?php
class CircasianTyraFighter extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 48*6;
        $this->faction = "Escalation Wars Circasian Empire";
        $this->phpclass = "CircasianTyraFighter";
        $this->shipClass = "Tyra Heavy flight";
        $this->imagePath = "img/ships/EscalationWars/CircasianTyra.png";
		$this->unofficial = true;

        $this->isd = 1967;
        
        $this->forwardDefense = 8;
        $this->sideDefense = 8;
        $this->freethrust = 8;
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
            $armour = array(3, 1, 2, 2);
            $fighter = new Fighter("CircasianTyraFighter", $armour, 12, $this->id);
            $fighter->displayName = "Tyra";
            $fighter->imagePath = "img/ships/EscalationWars/CircasianTyra.png";
            $fighter->iconPath = "img/ships/EscalationWars/CircasianTyra_Large.png";

			$gun = new PairedParticleGun(330, 30, 2, 3);
			$gun->displayName = "Paired Particle Beam";
			$fighter->addFrontSystem($gun);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
