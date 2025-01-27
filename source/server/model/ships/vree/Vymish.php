<?php
class Vymish extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 300;
		$this->faction = "Vree Conglomerate";
        $this->phpclass = "Vymish";
        $this->imagePath = "img/ships/VreeVymish.png";
        $this->shipClass = "Vymish Armed Trader";
  	    $this->canvasSize = 100;
	    $this->isCombatUnit = false; //not a combat unit, it will never be present in a regular battlegroup
	    
	    $this->isd = 2191;

        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        
        $this->turncost = 0.50;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 0;
        $this->gravitic = true;             
        
        $this->iniativebonus = 12 *5;

        
        $this->addPrimarySystem(new Reactor(3, 10, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 6, 6));
        $this->addPrimarySystem(new Engine(3, 9, 0, 6, 2));
		$this->addPrimarySystem(new Hangar(3, 2));
		$this->addPrimarySystem(new CargoBay(3, 20));		
		$this->addPrimarySystem(new GraviticThruster(3, 12, 0, 6, 3));
        $this->addPrimarySystem(new GraviticThruster(3, 12, 0, 6, 1)); 
        $this->addPrimarySystem(new GraviticThruster(3, 12, 0, 6, 2));   
        $this->addPrimarySystem(new GraviticThruster(3, 12, 0, 6, 4));                    
        $this->addPrimarySystem(new AntiprotonDefender(2, 0, 0, 0, 360));
        $this->addPrimarySystem(new AntimatterCannon(2, 0, 0, 0, 360));
		        
        $this->addFrontSystem(new AntiprotonDefender(2, 0, 0, 240, 0));
        $this->addFrontSystem(new AntiprotonDefender(2, 0, 0, 0, 120));

        $this->addAftSystem(new AntiprotonDefender(2, 0, 0, 180, 300));
        $this->addAftSystem(new AntiprotonDefender(2, 0, 0, 60, 180));        
       

		//structures
        $this->addPrimarySystem(new Structure(3, 60));
		
		$this->hitChart = array(
			0=> array(
				9 => "Cargo Bay",
				12 => "Scanner",
				15 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				6 => "TAG:Thruster",			
				9 => "TAG:Weapon",			
				17 => "Structure",
				20 => "Primary",
			),
			2=> array(
				6 => "TAG:Thruster",			
				9 => "TAG:Weapon",			
				17 => "Structure",
				20 => "Primary",
			),
		); 
    }
}
