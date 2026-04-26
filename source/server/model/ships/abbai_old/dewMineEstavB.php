<?php
class dewMineEstavB extends Mine{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 40;
		$this->faction = "Abbai Matriarchate (WotCR)";
        $this->phpclass = "dewMineEstavB";
        $this->imagePath = "img/ships/AbbaiWotcrMine.png";
        $this->shipClass = "Estav-B DEW Mine";
		$this->occurence = "common";
		$this->variantOf = "Estav-A DEW Mine";
        $this->isd = 2200;
        $this->notes = 'Has Command Controller';
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        $this->signature = 6;
        $this->detectedSignature = 3;           
        
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
		$ccIndex = array_search('MINE_CTRL', $this->enhancementOptionsEnabled);
		if ($ccIndex !== false) {
			unset($this->enhancementOptionsEnabled[$ccIndex]);
		}              

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));
        $this->addPrimarySystem(new MagGravReactorTechnical(0, 1, 0, 2));
        $this->addPrimarySystem(new mineStealth(0, 1, 1));
        $this->addPrimarySystem(new MineControllerDEW(0, 1, 0, 10, 4)); //$armour, $maxhealth, $powerReq, $startArc, $endArc, $range/output, $accuracy 
        $this->addPrimarySystem(new SensorSpear(0, 1, 1, 0, 360));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(0, 8));
        
        	//d20 hit chart
        $this->hitChart = array(
            0=> array(
                    20 => "Structure",
            )
        );
        
    }
}
?>
