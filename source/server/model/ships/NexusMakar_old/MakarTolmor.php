<?php
class MakarTolmor extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 18*6;
        $this->faction = "Nexus Makar Federation (early)";
        $this->phpclass = "MakarTolmor";
        $this->shipClass = "Tol Mor Armed Drone";
        $this->imagePath = "img/ships/Nexus/makar_tolmor.png";
		$this->unofficial = true;

        $this->isd = 1925;
        
        $this->forwardDefense = 7;
        $this->sideDefense = 7;
        $this->freethrust = 10;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 4;
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
            $armour = array(1, 1, 0, 0);
            $fighter = new Fighter("MakarTolmor", $armour, 5, $this->id);
            $fighter->displayName = "Tol Mor";
            $fighter->imagePath = "img/ships/Nexus/makar_tolmor.png";
            $fighter->iconPath = "img/ships/Nexus/makar_tolmor_large.png";

	        $light = new NexusLightDefenseGun(300, 60, 2); //$startArc, $endArc, $nrOfShots
	        $fighter->addFrontSystem($light);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
