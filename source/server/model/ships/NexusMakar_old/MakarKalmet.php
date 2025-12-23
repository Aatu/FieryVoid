<?php
class MakarKalmet extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 17*6;
        $this->faction = "Nexus Makar Federation (early)";
        $this->phpclass = "MakarKalmet";
        $this->shipClass = "Kalmet Armed Shuttle";
        $this->imagePath = "img/ships/Nexus/makar_kalmet2.png";
		$this->unofficial = true;

        $this->isd = 1902;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 11;
        $this->freethrust = 6;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
		$this->turndelay = 0;
        
		$this->hangarRequired = 'assault shuttles'; //for fleet check
        $this->iniativebonus = 60;
        $this->populate();       

        $this->enhancementOptionsEnabled[] = 'EXT_AMMO'; //To enable extra Ammo for main gun.

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(1, 0, 1, 1);
            $fighter = new Fighter("MakarKalmet", $armour, 15, $this->id);
            $fighter->displayName = "Kalmet";
            $fighter->imagePath = "img/ships/Nexus/makar_kalmet2.png";
            $fighter->iconPath = "img/ships/Nexus/makar_kalmet_large2.png";

	        $light = new NexusLightDefenseGun(300, 60, 1); //$startArc, $endArc, $nrOfShots
	        $fighter->addFrontSystem($light);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }

}
?>
