<?php
class dewMineTorrok extends Mine{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 24;
		$this->faction = "Escalation Wars Sshel'ath Alliance";
        $this->phpclass = "dewMineTorrok";
        $this->imagePath = "img/ships/eaMine.png";
        $this->shipClass = "Torrok DEW Mine";
		$this->occurence = "common";
		//$this->variantOf = 'NONE';
        $this->isd = 1921;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        $this->signature = 3;
        $this->detectedSignature = 1;           
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = -200; 
        $this->mineType = 'DEW';         
        $this->IFFSystem = true;
       		    	    	    	    
        //Block all enhancements for Mine units when bought
		Enhancements::nonstandardEnhancementSet($this, 'Mines');
        
        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));
        $this->addPrimarySystem(new MagGravReactorTechnical(0, 1, 0, 2));
        $this->addPrimarySystem(new mineStealth(0, 1, 1));
        $this->addPrimarySystem(new MineControllerDEW(0, 1, 0, 5, 4)); //$armour, $maxhealth, $powerReq, $startArc, $endArc, $range/output, $accuracy
        $this->addPrimarySystem(new EWGatlingLaser(0, 1, 0, 0, 360));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(2, 10));
        
        	//d20 hit chart
        $this->hitChart = array(
            0=> array(
                    20 => "Structure",
            )
        );
        
    }
}
?>
