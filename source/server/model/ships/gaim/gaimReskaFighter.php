<?php
class gaimReskaFighter extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 38*6;
        $this->faction = "Gaim";
        $this->phpclass = "gaimReskaFighter";
        $this->shipClass = "Reska Light flight";
        $this->imagePath = "img/ships/GaimReska.png";
		
        $this->isd = 2250;
        
        $this->forwardDefense = 5;
        $this->sideDefense = 5;
        $this->freethrust = 9;
        $this->offensivebonus = 4;
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
            $armour = array(2, 1, 1, 1);
            $fighter = new Fighter("gaimReskaFighter", $armour, 9, $this->id);
            $fighter->displayName = "Reska";
            $fighter->imagePath = "img/ships/GaimReska.png";
            $fighter->iconPath = "img/ships/GaimReska_large.png";

			$gun = new PairedParticleGun(330, 30, 2);
			$gun->displayName = "Light Particle Gun";
			$fighter->addFrontSystem($gun);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
