<?php
class dewMine32 extends Mine{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 30;
		$this->faction = "Torata Regency";
        $this->phpclass = "dewMine32";
        $this->imagePath = "img/ships/torataMine.png";
        $this->shipClass = "Class 3-2 DEW Mine";
		$this->occurence = "common";
		$this->variantOf = "Class 3-1 DEW Mine";
        $this->isd = 2256;
        $this->notes .= 'Has Command Controller';            
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        $this->signature = 4;
        $this->detectedSignature = 2;           
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = -200; 
        $this->mineType = 'DEW';  
        $this->commandControl = true;                
       		    	    	    	    
        //Block all enhancements for Mine units when bought
		Enhancements::nonstandardEnhancementSet($this, 'Mines');
        
		// Remove MINE_CTRL from enabled array because it's added by the 'Mines' set, but this unit already has one.
		$contIndex = array_search('MINE_CTRL', $this->enhancementOptionsEnabled);
		if ($contIndex !== false) {
			unset($this->enhancementOptionsEnabled[$contIndex]);
		}                

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));
        $this->addPrimarySystem(new MagGravReactorTechnical(0, 1, 0, 2));
        $this->addPrimarySystem(new mineStealth(0, 1, 1));
        $this->addPrimarySystem(new MineControllerDEW(0, 1, 0, 4, 5)); //$armour, $maxhealth, $powerReq, $startArc, $endArc, $range/output, $accuracy 
        $this->addPrimarySystem(new PentagonArray(0, 1, 1, 0, 360));       
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(0, 5));
        
        	//d20 hit chart
        $this->hitChart = array(
            0=> array(
                    20 => "Structure",
            )
        );
        
    }
}
?>
