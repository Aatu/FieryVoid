<?php
class crusaderGC extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 580;
		$this->faction = "Great Crusade Orieni Imperium";
        $this->phpclass = "crusaderGC";
        $this->imagePath = "img/ships/GCenlightenment.png";
        $this->shipClass = "Crusader Carrier (2245)";
			$this->variantOf = "Enlightenment Invader (2237)";
			$this->occurence = "uncommon";
        $this->limited = 33;
        $this->shipSizeClass = 3;
	    $this->isd = 2245;
        $this->fighters = array("light"=>36, "medium"=>12);
        $this->canvasSize = 215;

		$this->unofficial = true;
		
        $this->forwardDefense = 19;
        $this->sideDefense = 18;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1;
        $this->accelcost = 5;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        
        $this->addPrimarySystem(new Reactor(5, 30, 0, 0));
        $this->addPrimarySystem(new CnC(5, 25, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 25, 4, 7));
        $this->addPrimarySystem(new Engine(5, 25, 0, 8, 4));
        $this->addPrimarySystem(new Hangar(4, 38, 24));
        $this->addPrimarySystem(new HKControlNodeOrieni(5, 24, 4, 4));
              
        $this->addFrontSystem(new WarLance(3, 9, 6, 240, 60));
        $this->addFrontSystem(new WarLance(3, 9, 6, 300, 120));
        $this->addFrontSystem(new ImpRapidGatling(2, 4, 2, 240, 120));
        $this->addFrontSystem(new ImpRapidGatling(2, 4, 2, 240, 120));
        $this->addFrontSystem(new ImpRapidGatling(2, 4, 2, 240, 120));
        $this->addFrontSystem(new Thruster(3, 15, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 15, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 15, 0, 2, 1));  

        $this->addAftSystem(new ImpRapidGatling(2, 4, 2, 60, 300));
        $this->addAftSystem(new ImpRapidGatling(2, 4, 2, 60, 300));
        $this->addAftSystem(new Thruster(3, 12, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 2, 2));
        $this->addAftSystem(new JumpEngine(5, 12, 4, 20));

        $this->addLeftSystem(new ImpRapidGatling(2, 4, 2, 180, 360));
        $this->addLeftSystem(new ImpRapidGatling(2, 4, 2, 180, 360));
        $this->addLeftSystem(new Hangar(3, 12, 6));        
        $this->addLeftSystem(new Thruster(4, 20, 0, 6, 3));

        $this->addRightSystem(new ImpRapidGatling(2, 4, 2, 0, 180));
        $this->addRightSystem(new ImpRapidGatling(2, 4, 2, 0, 180));
        $this->addRightSystem(new Hangar(3, 12, 6));        
        $this->addRightSystem(new Thruster(4, 20, 0, 6, 4));

		//structures
        $this->addFrontSystem(new Structure(5, 51));
        $this->addAftSystem(new Structure(5, 54));
        $this->addLeftSystem(new Structure(5, 60));
        $this->addRightSystem(new Structure(5, 60));
        $this->addPrimarySystem(new Structure(5, 48));

	//d20 hit chart
	$this->hitChart = array(
		
		//PRIMARY
		0=> array( 
			8 => "Structure",
			10 => "HK Control Node",
			13 => "Scanner",
			15 => "Engine",
			18 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),
		//Forward
		1=> array(
			6 => "Thruster",
			8 => "War Lance",
			11 => "Improved Gatling Railgun",
			18 => "Structure",
			20 => "Primary",
		),
		//Aft
		2=> array(
			8 => "Thruster",
			10 => "Improved Gatling Railgun",
			13 => "Jump Engine",
			18 => "Structure",
			20 => "Primary",
		),
		//Port
		3=> array(
			6 => "Thruster",
			8 => "Improved Gatling Railgun",
			11 => "Hangar",			
			18 => "Structure",
			20 => "Primary",
		),
		//Starboard
		4=> array(
			6 => "Thruster",
			8 => "Improved Gatling Railgun",
			11 => "Hangar",			
			18 => "Structure",
			20 => "Primary",
		),
	);

	    
	    
    }
}



?>
