<?php
class gaimKastaFighter extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 44*6;
        $this->faction = "Gaim";
        $this->phpclass = "gaimKastaFighter";
        $this->shipClass = "Kasta Missile flight";
        $this->variantOf = "Koist Medium flight";		
		$this->occurence = "uncommon";
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
            $fighter = new Fighter("gaimKastaFighter", $armour, 11, $this->id);
            $fighter->displayName = "Kasta";
            $fighter->imagePath = "img/ships/GaimKoist.png";
            $fighter->iconPath = "img/ships/GaimKoist_large.png";

			$gun = new PairedParticleGun(330, 30, 2);
			$gun->displayName = "Light Particle Gun";
			$fighter->addFrontSystem($gun);
            $fighter->addFrontSystem(new FighterMissileRack(4, 330, 30));
			
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
