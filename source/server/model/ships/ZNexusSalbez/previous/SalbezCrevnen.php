<?php
class SalbezCrevnen extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 17*6;
        $this->faction = "ZNexus Playtest Sal-bez";
        $this->phpclass = "SalbezCrevnen";
        $this->shipClass = "Crev-nen Attack Shuttle Heavy Flight";
        $this->imagePath = "img/ships/Nexus/salbez_crevnen.png";
		$this->unofficial = true;

        $this->isd = 1999;
        
        $this->forwardDefense = 9;
        $this->sideDefense = 10;
        $this->freethrust = 6;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 0;
        $this->turncost = 0.33;
		$this->turndelay = 0.2;
        
        $this->iniativebonus = 80;
        $this->populate();       

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(1, 0, 1, 1);
            $fighter = new Fighter("SalbezCrevnen", $armour, 12, $this->id);
            $fighter->displayName = "Crev-nen";
            $fighter->imagePath = "img/ships/Nexus/salbez_crevnen.png";
            $fighter->iconPath = "img/ships/Nexus/salbez_crevnen_large.png";

	        $light = new NexusParticleGridFtr(270, 90, 1); //$startArc, $endArc, $nrOfShots
	        $fighter->addFrontSystem($light);
			
			$aft = new LightParticleBeam(150, 210, 1, 1);
			$fighter->addAftSystem($aft);
			
//			$missile = new NexusDartFtr(2, 330, 30);
//			$missile->firingModes = array(1="DI");
//			$missile->iconPath = "NexusDartInterceptor.png";
//			$missile->displayName = "Dart Interceptor";
//			$missile->missileArray = array(1=> new 
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
