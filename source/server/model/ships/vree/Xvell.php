<?php
class Xvell extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 350;
		$this->faction = "Vree";
        $this->phpclass = "Xvell";
        $this->imagePath = "img/ships/VreeXvell.png";
        $this->shipClass = "Xvell Escort Saucer";
  		$this->canvasSize = 100;        
	    
	    $this->isd = 2215;

        $this->forwardDefense = 11;
        $this->sideDefense = 11;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 0;
        
        $this->iniativebonus = 12 *5;
        $this->agile = true;

        
        $this->addPrimarySystem(new Reactor(3, 11, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 6, 6));
        $this->addPrimarySystem(new Engine(3, 9, 0, 6, 2));
		$this->addPrimarySystem(new Hangar(3, 1));	
		$this->addPrimarySystem(new Thruster(3, 10, 0, 6, 3));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 6, 4));        
        $this->addPrimarySystem(new Thruster(3, 10, 0, 6, 1));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 6, 2)); 
        		        
        $this->addFrontSystem(new AntiprotonGun(3, 0, 0, 240, 0));
        $this->addFrontSystem(new AntiprotonGun(3, 0, 0, 0, 120));

        $this->addAftSystem(new AntiprotonGun(3, 0, 0, 180, 300));
        $this->addAftSystem(new AntiprotonGun(3, 0, 0, 60, 180));        
       

		//structures
        $this->addPrimarySystem(new Structure(3, 40));
		
		$this->hitChart = array(
			0=> array(
				5 => "Scanner",
				10 => "Engine",
				13 => "Hangar",
				18 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				6 => "0:Thruster",				
				9 => "Antiproton Gun",			
				17 => "Structure",
				20 => "Primary",
			),
			2=> array(
				6 => "0:Thruster",				
				9 => "Antiproton Gun",				
				17 => "Structure",
				20 => "Primary",
			),
		); 
    }
}
