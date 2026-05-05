<?php
class dewMineOcaraE extends Mine{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 110;
	    $this->faction = "Centauri Republic";
        $this->phpclass = "dewMineOcaraE";
        $this->imagePath = "img/ships/centauriMine.png";
        $this->shipClass = "Ocara-E DEW Mine";
		$this->occurence = "common";
		$this->variantOf = "Ocara-A DEW Mine";
        $this->isd = 2200;
        $this->notes = '<br>Has Flexible Targeting';
        $this->multiSettings = true;                    
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        $this->signature = 1;
        $this->detectedSignature = -2;           
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = -200; 
        $this->mineType = 'DEW';         
       		    	    	    	    
        //Block all enhancements for Mine units when bought
		Enhancements::nonstandardEnhancementSet($this, 'Mines');	
		
		// Remove MINE_MULTI from enabled array because it's added by the 'Mines' set, but this unit already has one.
		$mmIndex = array_search('MINE_MULTI', $this->enhancementOptionsEnabled);
		if ($mmIndex !== false) {
			unset($this->enhancementOptionsEnabled[$mmIndex]);
		}   

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));
        $this->addPrimarySystem(new MagGravReactorTechnical(0, 1, 0, 4));
        $this->addPrimarySystem(new mineStealth(0, 1, 1));
        $this->addPrimarySystem(new MineControllerDEW(0, 1, 0, 15, 5)); //$armour, $maxhealth, $powerReq, $startArc, $endArc, $range/output, $accuracy 
        $this->addPrimarySystem(new BattleLaser(0, 1, 1, 0, 360));
        $this->addPrimarySystem(new TwinArray(0, 1, 1, 0, 360));
        $this->addPrimarySystem(new TwinArray(0, 1, 1, 0, 360));                    
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(2, 15));
        
        	//d20 hit chart
        $this->hitChart = array(
            0=> array(
                    20 => "Structure",
            )
        );
        
    }
}
?>
