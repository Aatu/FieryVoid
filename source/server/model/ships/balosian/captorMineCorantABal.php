<?php
class captorMineCorantABal extends Mine{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 10;
		$this->faction = "Balosian Underdwellers";
        $this->phpclass = "captorMineCorantABal";
        $this->imagePath = "img/ships/balosianMine.png";
        $this->shipClass = "Corant-A Captor Mine";
		$this->occurence = "common";
		//$this->variantOf = "Corant-A Captor Mine";
        $this->isd = 2208;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        $this->signature = 3;         
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = -200; 
        $this->mineType = 'Captor';         
       		    	    	    	    
        //Block all enhancements for Mine units when bought
		Enhancements::nonstandardEnhancementSet($this, 'Mines');	 

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));
        $this->addPrimarySystem(new MagGravReactorTechnical(0, 1, 0, 2));
        $this->addPrimarySystem(new mineStealth(0, 1, 1));
        $this->addPrimarySystem(new CaptorMine(0, 1, 1, 0, 360, 3, 5, 1, 0, 12)); //$armour, $maxhealth, $powerReq, $startArc, $endArc, $range, $accuracy, $diceType, $dice, $damageBonus 
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(0, 1));
        
        	//d20 hit chart
        $this->hitChart = array(
            0=> array(
                    20 => "Structure",
            )
        );
        
    }
}
?>
