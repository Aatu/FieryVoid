<?php
class ChoukaRaiderReclumAFighter extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 28*6;
        $this->faction = "ZEscalation Chouka Raider";
        $this->phpclass = "ChoukaRaiderReclumAFighter";
        $this->shipClass = "Reclum-A Light flight";
        $this->imagePath = "img/ships/EscalationWars/ChoukaRaiderReclum.png";
        $this->customFtrName = "Reclum-A";
		$this->unofficial = true;
		
        $this->isd = 1890;
        
        $this->forwardDefense = 6;
        $this->sideDefense = 6;
        $this->freethrust = 11;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 10;
        $this->turncost = 0.33;
		$this->turndelay = 0;
        
        $this->iniativebonus = 100;
        $this->populate();       

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(1, 0, 1, 1);
            $fighter = new Fighter("ChoukaRaiderReclumAFighter", $armour, 7, $this->id);
            $fighter->displayName = "Reclum-A Light Fighter";
            $fighter->imagePath = "img/ships/EscalationWars/ChoukaRaiderReclum.png";
            $fighter->iconPath = "img/ships/EscalationWars/ChoukaRaiderReclum_Large.png";

			$gun = new LightParticleBeam(330, 30, 1);
			$gun->displayName = "Ultralight Particle Beam";
			$fighter->addFrontSystem($gun);
		
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
