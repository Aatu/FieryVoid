<?php
class captorMineGuthrenB extends Mine{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 13;
	    $this->faction = "Nexus Makar Federation";
        $this->phpclass = "captorMineGuthrenB";
        $this->imagePath = "img/ships/centauriMine.png";
        $this->shipClass = "Guthren-B Captor Mine";
		$this->occurence = "common";
        $this->isd = 2105;
		$this->unofficial = true;
 
        $this->notes = 'Has IFF System';  
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        $this->signature = 2;         
        
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
        $this->addPrimarySystem(new CaptorMine(0, 1, 1, 0, 360, 3, 4, 4, 0, 16)); //$armour, $maxhealth, $powerReq, $startArc, $endArc, $range, $accuracy, $diceType, $dice, $damageBonus 
        
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
