<?php
class SalbezNewFighter extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 43*6;
        $this->faction = "ZNexus Playtest Sal-bez";
        $this->phpclass = "SalbezNewFighter";
        $this->shipClass = "New Fighter";
        $this->imagePath = "img/ships/Nexus/salbez_new_fighter.png";
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
            $fighter = new Fighter("SalbezNewFighter", $armour, 12, $this->id);
            $fighter->displayName = "New Fighter";
            $fighter->imagePath = "img/ships/Nexus/salbez_new_fighter.png";
            $fighter->iconPath = "img/ships/Nexus/salbez_new_fighter_large.png";

	        $light = new LightParticleBeamFtr(300, 60, 1); //$startArc, $endArc, $nrOfShots
	        $fighter->addFrontSystem($light);

			$aft = new LightParticleBeam(150, 210, 1, 1);
			$fighter->addAftSystem($aft);
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
