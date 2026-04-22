<?php
class dewMineD1 extends Mine{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 15;
		$this->faction = "Earth Alliance";
        $this->phpclass = "dewMineD1";
        $this->imagePath = "img/ships/eaMine.png";
        $this->shipClass = "Class-D1 DEW Mine";
		$this->occurence = "common";
		//$this->variantOf = 'NONE';
        $this->isd = 2200;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        $this->signature = 4;
        $this->detectedSignature = 1;           
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = -200; 
        $this->mineType = 'DEW';         
       		    	    	    	    
        //Block all enhancements for Mine units when bought
		Enhancements::nonstandardEnhancementSet($this, 'Mines');	 

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));
        $this->addPrimarySystem(new MagGravReactorTechnical(0, 1, 0, 2));
        $this->addPrimarySystem(new mineStealth(0, 1, 1));
        $this->addPrimarySystem(new MineControllerDEW(0, 1, 0, 6, 8, false, array('Fighters'))); //$armour, $maxhealth, $powerReq, $startArc, $endArc, $range/output, $accuracy, ballistic, target types 
        $this->addPrimarySystem(new InterceptorMkI(0, 1, 1, 0, 360, true));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(2, 5));
        
        	//d20 hit chart
        $this->hitChart = array(
            0=> array(
                    20 => "Structure",
            )
        );
        
    }
}
?>
