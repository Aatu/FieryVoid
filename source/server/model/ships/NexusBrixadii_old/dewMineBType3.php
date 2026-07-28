<?php
class dewMineBtype3 extends Mine{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 31;
        $this->faction = "Nexus Brixadii Clans (early)";
        $this->phpclass = "dewMineBtype3";
        $this->imagePath = "img/ships/descariMine.png";
        $this->shipClass = "Heavy Particle Projector DEW Mine";
		$this->occurence = "common";
        $this->isd = 2048;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        $this->signature = 2;
        $this->detectedSignature = -1;           
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = -200; 
        $this->mineType = 'DEW';         
       		    	    	    	    
        //Block all enhancements for Mine units when bought
		Enhancements::nonstandardEnhancementSet($this, 'Mines');	 

		// Remove IFF_SYS from enabled array because it's added by the 'Mines' set, but this unit already has one.
		$iffIndex = array_search('IFF_SYS', $this->enhancementOptionsEnabled);
		if ($iffIndex !== false) {
			unset($this->enhancementOptionsEnabled[$iffIndex]);
		}

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));
        $this->addPrimarySystem(new MagGravReactorTechnical(0, 1, 0, 2));
        $this->addPrimarySystem(new mineStealth(0, 1, 1));
        $this->addPrimarySystem(new MineControllerDEW(0, 1, 0, 9, 4)); //$armour, $maxhealth, $powerReq, $startArc, $endArc, $range/output, $accuracy 
        $this->addPrimarySystem(new HvyParticleProjector(0, 1, 0, 0, 360));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(2, 12));
        
        	//d20 hit chart
        $this->hitChart = array(
            0=> array(
                    20 => "Structure",
            )
        );
        
    }
}
?>
