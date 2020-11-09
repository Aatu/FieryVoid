<?php
class gaimKoistFighter extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 40*6;
        $this->faction = "Gaim";
        $this->phpclass = "gaimKoistFighter";
        $this->shipClass = "Koist Medium flight";
        $this->imagePath = "img/ships/GaimKoist.png";
		
        $this->isd = 2252;
        
        $this->forwardDefense = 7;
        $this->sideDefense = 8;
        $this->freethrust = 10;
        $this->offensivebonus = 4;
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
            $armour = array(2, 1, 3, 3);
            $fighter = new Fighter("gaimKoistFighter", $armour, 11, $this->id);
            $fighter->displayName = "Koist";
            $fighter->imagePath = "img/ships/GaimKoist.png";
            $fighter->iconPath = "img/ships/GaimKoist_large.png";

			$gun = new LightParticleBeam(330, 30, 3);
			$gun->displayName = "Light Particle Beam";
			$fighter->addFrontSystem($gun);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
