<?php
class SalbezUrzchk extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 37*6;
        $this->faction = "Nexus Sal-bez Coalition (early)";
        $this->phpclass = "SalbezUrzchk";
        $this->shipClass = "Urz'chk Heavy Flight";
        $this->imagePath = "img/ships/Nexus/salbez_urzchk3.png";
		$this->unofficial = true;

        $this->isd = 2063;
        
        $this->forwardDefense = 9;
        $this->sideDefense = 9;
        $this->freethrust = 8;
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
            $armour = array(2, 2, 2, 2);
            $fighter = new Fighter("SalbezUrzchk", $armour, 11, $this->id);
            $fighter->displayName = "Urz'chk";
            $fighter->imagePath = "img/ships/Nexus/salbez_urzchk3.png";
            $fighter->iconPath = "img/ships/Nexus/salbez_urzchk_large3.png";

	        $light = new HvyParticleGunFtr(300, 60, 1); //$startArc, $endArc, $nrOfShots
	        $fighter->addFrontSystem($light);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
