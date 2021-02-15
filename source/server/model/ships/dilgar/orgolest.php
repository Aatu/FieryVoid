<?php
class Orgolest extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 100;
		$this->faction = "Dilgar";
        $this->phpclass = "Orgolest";
        $this->imagePath = "img/ships/orgolest.png";
        $this->shipClass = "Orgolest Satellite";
                $this->isd = 2230;
        
        $this->forwardDefense = 8;
        $this->sideDefense = 8;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60; 

        $this->addPrimarySystem(new Reactor(3, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 4, 2, 4)); 
        $this->addPrimarySystem(new Thruster(2, 4, 0, 0, 2));
        
        $this->addPrimarySystem(new QuadPulsar(2, 10, 4, 270, 90));
        $this->addPrimarySystem(new ScatterPulsar(1, 4, 2, 0, 360));
        $this->addPrimarySystem(new QuadPulsar(2, 10, 4, 270, 90));
        
        $this->addPrimarySystem(new LightLaser(1, 4, 3, 180, 360));
        $this->addPrimarySystem(new LightLaser(1, 4, 3, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(2, 25));
        
        	//d20 hit chart
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
                    10 => "Thruster",
		    		13 => "Quad Pulsar",
		    		15 => "Light Laser",
                    17 => "Scanner",
                    19 => "Reactor",
                    20 => "Scatter Pulsar",
            )
        );
        
    }
}