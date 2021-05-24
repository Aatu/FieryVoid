<?php
class SalbezUrzchkRefit extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 42*6;
        $this->faction = "ZNexus Playtest Sal-bez";
        $this->phpclass = "SalbezUrzchkRefit";
        $this->shipClass = "Urz'chk Heavy Flight (2102 refit)";
			$this->variantOf = "Urz'chk Heavy Flight";
			$this->occurence = "common";
        $this->imagePath = "img/ships/Nexus/salbez_urzchk.png";
		$this->unofficial = true;

        $this->isd = 2102;
        
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
            $armour = array(2, 2, 2, 2);
            $fighter = new Fighter("SalbezUrzchk", $armour, 11, $this->id);
            $fighter->displayName = "Urz'chk";
            $fighter->imagePath = "img/ships/Nexus/salbez_urzchk.png";
            $fighter->iconPath = "img/ships/Nexus/salbez_urzchk_large.png";

	        $light = new LightParticleBeamFtr(300, 60, 1); //$startArc, $endArc, $nrOfShots
	        $fighter->addFrontSystem($light);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
