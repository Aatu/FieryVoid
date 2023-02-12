<?php
class SalbezDrazchk extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 43*6;
        $this->faction = "ZNexus Sal-bez";
        $this->phpclass = "SalbezDrazchk";
        $this->shipClass = "Draz-chk Heavy Flight";
        $this->imagePath = "img/ships/Nexus/salbez_drazchk.png";
		$this->unofficial = true;

        $this->isd = 2120;
        
        $this->forwardDefense = 9;
        $this->sideDefense = 9;
        $this->freethrust = 10;
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
            $armour = array(3, 3, 2, 2);
            $fighter = new Fighter("SalbezDrazchk", $armour, 12, $this->id);
            $fighter->displayName = "Draz-chk";
            $fighter->imagePath = "img/ships/Nexus/salbez_drazchk.png";
            $fighter->iconPath = "img/ships/Nexus/salbez_drazchk_large.png";

	        $light = new HvyParticleGunFtr(300, 60, 1); //$startArc, $endArc, $nrOfShots
	        $fighter->addFrontSystem($light);

			$aft = new PairedParticleGun(150, 210, 1, 1);
			$fighter->addAftSystem($aft);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
