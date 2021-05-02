<?php
class VelraxTassrivInterceptorA extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 30*6;
        $this->faction = "ZNexus Velrax";
        $this->phpclass = "VelraxTassrivInterceptorA";
        $this->shipClass = "Tassriv Interceptor-A flight";
        $this->imagePath = "img/ships/Nexus/VelraxTassriv_v2.png";
		$this->unofficial = true;

        $this->isd = 2061;
        
        $this->forwardDefense = 6;
        $this->sideDefense = 6;
        $this->freethrust = 11;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 10;
        $this->turncost = 0.33;
		$this->turndelay = 0;
        
        $this->iniativebonus = 105;
        $this->populate();       

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(2, 1, 1, 1);
            $fighter = new Fighter("VelraxTassrivInterceptorA", $armour, 9, $this->id);
            $fighter->displayName = "Tassriv-A";
            $fighter->imagePath = "img/ships/Nexus/VelraxTassriv_v2.png";
            $fighter->iconPath = "img/ships/Nexus/VelraxTassriv_Large.png";

	        $light = new NexusLightIonGun(330, 30, 0); //$startArc, $endArc, $nrOfShots
	        $fighter->addFrontSystem($light);
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
