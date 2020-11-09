<?php
class gaimKrastFighter extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 37*6;
        $this->faction = "Gaim";
        $this->phpclass = "gaimKrastFighter";
        $this->shipClass = "Krast Recon flight";
        $this->imagePath = "img/ships/GaimKoist.png";
		
        $this->isd = 2252;
        
        $this->forwardDefense = 7;
        $this->sideDefense = 8;
        $this->freethrust = 9;
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
            $armour = array(2, 1, 2, 2);
            $fighter = new Fighter("gaimKrastFighter", $armour, 11, $this->id);
            $fighter->displayName = "Krast";
            $fighter->imagePath = "img/ships/GaimKoist.png";
            $fighter->iconPath = "img/ships/GaimKoist_large.png";

			$gun = new PairedParticleGun(330, 30, 2);
			$gun->displayName = "Light Particle Gun";
			$fighter->addFrontSystem($gun);
            	$missileRack = new FighterMissileRack(2, 330, 30);
            	$missileRack->firingModes = array( 1 => "FY" );
            	$missileRack->missileArray = array( 1 => new MissileFY(330, 30) );
			$fighter->addFrontSystem($missileRack);	
			
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
