<?php
class VelraxCasrissAFighter extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 20*6;
        $this->faction = "ZNexus Velrax";
        $this->phpclass = "VelraxCasrissAFighter";
        $this->shipClass = "Casriss-A flight";
        $this->imagePath = "img/ships/Nexus/VelraxCasriss.png";
		$this->unofficial = true;

        $this->isd = 2033;
        
        $this->forwardDefense = 5;
        $this->sideDefense = 6;
        $this->freethrust = 8;
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
            $armour = array(1, 1, 0, 0);
            $fighter = new Fighter("VelraxCasrissAFighter", $armour, 7, $this->id);
            $fighter->displayName = "Casriss-A";
            $fighter->imagePath = "img/ships/Nexus/VelraxCasriss.png";
            $fighter->iconPath = "img/ships/Nexus/VelraxCasriss_Large.png";

	        $light = new NexusLightIonGun(330, 30, 0); //$startArc, $endArc, $nrOfShots
	        $fighter->addFrontSystem($light);
			
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
