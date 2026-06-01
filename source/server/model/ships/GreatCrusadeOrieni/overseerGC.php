<?php
class overseerGC extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 700;
		$this->faction = "Great Crusade Orieni Imperium";
        $this->phpclass = "overseerGC";
        $this->imagePath = "img/ships/GCvigilant.png";
        $this->shipClass = "Overseer HK Conveyor";
			$this->variantOf = "Vigilant Support Ship";
			$this->occurence = "uncommon";
	    $this->isd = 250;
        $this->shipSizeClass = 3;
        $this->fighters = array("light"=>6, "medium"=>24);
        $this->canvasSize = 190;

		$this->unofficial = true;
		
        $this->forwardDefense = 17;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 3;

        $this->addPrimarySystem(new Reactor(5, 20, 0, 0));
        $this->addPrimarySystem(new CnC(6, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 25, 5, 8));
        $this->addPrimarySystem(new Engine(5, 25, 0, 9, 4));
        $this->addPrimarySystem(new Hangar(4, 8, 6));
        $this->addPrimarySystem(new JumpEngine(5, 30, 6, 25));
        $this->addPrimarySystem(new HKControlNode(5, 12, 4, 4));
        $this->addPrimarySystem(new WarLance(4, 9, 6, 0, 360));
        
        $this->addFrontSystem(new Thruster(3, 12, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 12, 0, 3, 1));
        $this->addFrontSystem(new HeavyGaussRifle(4, 12, 5, 240, 60));
        $this->addFrontSystem(new HeavyGaussRifle(4, 12, 5, 300, 120));
        $this->addFrontSystem(new ImpRapidGatling(2, 4, 2, 240, 120));
        $this->addFrontSystem(new ImpRapidGatling(2, 4, 2, 240, 120));
        $this->addFrontSystem(new ImpRapidGatling(2, 4, 2, 240, 120));

        $this->addAftSystem(new ImpRapidGatling(2, 4, 2, 60, 300));
        $this->addAftSystem(new ImpRapidGatling(2, 4, 2, 60, 300));
        $this->addAftSystem(new Thruster(3, 15, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 3, 2));
		
        $this->addLeftSystem(new Thruster(4, 20, 0, 6, 3));
        $this->addLeftSystem(new Hangar(2, 6, 6));
        $this->addLeftSystem(new Hangar(2, 6, 6));
        $this->addLeftSystem(new ImpRapidGatling(2, 4, 2, 180, 360));
        $this->addLeftSystem(new ImpRapidGatling(2, 4, 2, 180, 360));

        $this->addRightSystem(new Thruster(4, 20, 0, 6, 4));
        $this->addRightSystem(new Hangar(2, 6, 6));
        $this->addRightSystem(new Hangar(2, 6, 6));
        $this->addRightSystem(new ImpRapidGatling(2, 4, 2, 0, 180));
        $this->addRightSystem(new ImpRapidGatling(2, 4, 2, 0, 180));

		//structures
        $this->addFrontSystem(new Structure(4, 48));
        $this->addAftSystem(new Structure(4, 44));
        $this->addLeftSystem(new Structure(4, 45));
        $this->addRightSystem(new Structure(4, 45));
        $this->addPrimarySystem(new Structure(5, 56));

	//d20 hit chart
	$this->hitChart = array(
		
		//PRIMARY
		0=> array( 
			7 => "Structure",
			8 => "War Lance",
			10 => "Jump Engine",
			12 => "Scanner",
			14 => "Engine",
			15 => "HK Control Node",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),

		//Forward
		1=> array(
			4 => "Thruster",
			8 => "Class-S Missile Rack",
			11 => "Improved Gatling Railgun",
			18 => "Structure",
			20 => "Primary",
		),

		//Aft
		2=> array(
			6 => "Thruster",
			9 => "Improved Gatling Railgun",
			18 => "Structure",
			20 => "Primary",
		),

		//Port
		3=> array(
			4 => "Thruster",
			6 => "Improved Gatling Railgun",
			11 => "Cargo Bay",
			18 => "Structure",
			20 => "Primary",
		),

		//Starboard
		4=> array(
			4 => "Thruster",
			6 => "Improved Gatling Railgun",
			11 => "Cargo Bay",
			18 => "Structure",
			20 => "Primary",
		),

	);

        
    }
}
?>
