<?php
class mineHFF extends Mine{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 12;
        $this->faction = "Hurr Republic";
        $this->phpclass = "dewMineD2";
        $this->imagePath = "img/ships/hurrMine.png";
        $this->shipClass = "Type-FF Special Mine";
		$this->occurence = "uncommon";
		//$this->variantOf = "Class-D2 DEW Mine";
        $this->isd = 2200;
        $this->fighters = array("light" => 1);        
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        $this->signature = 0;        
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = -200; 
        $this->mineType = '';         
       		    	    	    	    
        //Block all enhancements for Mine units when bought
		Enhancements::nonstandardEnhancementSet($this, 'Mines');	 

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));
        $this->addPrimarySystem(new MagGravReactorTechnical(0, 1, 0, 1));
        $this->addPrimarySystem(new mineStealth(0, 1, 1));
        $this->addPrimarySystem(new Hangar(0, 1)); 
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(0, 10));
        
        	//d20 hit chart
        $this->hitChart = array(
            0=> array(
                    20 => "Structure",
            )
        );
        
    }
}
?>
