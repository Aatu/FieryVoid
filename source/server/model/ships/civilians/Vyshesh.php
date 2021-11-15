<?php
class Vyshesh extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 120;
		$this->faction = "Civilians";
        $this->phpclass = "Vyshesh";
        $this->imagePath = "img/ships/VreeVymish.png";
        $this->shipClass = "Vree Vyshesh Free Trader";
  	    $this->canvasSize = 100;        
	    
	    $this->isd = 2172;

        $this->forwardDefense = 13;
        $this->sideDefense = 13;
        
        $this->turncost = 0.75;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 0;
        $this->gravitic = true;             
        
        $this->iniativebonus = -4 *5;

        
        $this->addPrimarySystem(new Reactor(3, 8, 0, 0));
        $this->addPrimarySystem(new CnC(3, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 6, 2, 3));
        $this->addPrimarySystem(new Engine(3, 9, 0, 6, 3));
		$this->addPrimarySystem(new Hangar(2, 2));
		$this->addPrimarySystem(new CargoBay(1, 25));		
		$this->addPrimarySystem(new GraviticThruster(2, 10, 0, 6, 3));
        $this->addPrimarySystem(new GraviticThruster(2, 10, 0, 6, 4));
        $this->addPrimarySystem(new GraviticThruster(2, 10, 0, 6, 1)); 
        $this->addPrimarySystem(new GraviticThruster(2, 10, 0, 6, 2));                        
        $this->addPrimarySystem(new AntiprotonDefender(2, 0, 0, 0, 360));
		        
        $this->addFrontSystem(new CargoBay(1, 25));
        $this->addFrontSystem(new CargoBay(1, 25));

        $this->addAftSystem(new CargoBay(1, 25));
        $this->addAftSystem(new CargoBay(1, 25));       
       

		//structures
        $this->addPrimarySystem(new Structure(3, 55));
		
		$this->hitChart = array(
			0=> array(
				9 => "Cargo Bay",
				10 => "Antiproton Defender",
				12 => "Scanner",
				15 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				6 => "0:Thruster",			
				9 => "Cargo Bay",			
				17 => "Structure",
				20 => "Primary",
			),
			2=> array(
				6 => "0:Thruster",			
				9 => "Cargo Bay",			
				17 => "Structure",
				20 => "Primary",
			),
		); 
    }
}
