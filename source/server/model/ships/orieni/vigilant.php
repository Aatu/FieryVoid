<?php
class Vigilant extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 640;
		$this->faction = "Orieni";
        $this->phpclass = "Vigilant";
        $this->imagePath = "img/ships/vigilant.png";
        $this->shipClass = "Vigilant Combat Support Ship";
	    $this->isd = 2007;
        $this->shipSizeClass = 3;
        $this->fighters = array("light"=>6, "minesweeping shuttles"=>6);
        $this->canvasSize = 200;
		
        $this->forwardDefense = 17;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->limited = 10;

        
        $this->addPrimarySystem(new Reactor(5, 20, 0, 4));
        $this->addPrimarySystem(new CnC(6, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 25, 5, 6)); //+4 minesweeping
        $this->addPrimarySystem(new Engine(5, 25, 0, 9, 4));
        $this->addPrimarySystem(new Hangar(4, 14, 6));
        $this->addPrimarySystem(new JumpEngine(5, 30, 6, 25));
        $this->addPrimarySystem(new HKControlNode(5, 12, 1, 1));
        $this->addPrimarySystem(new SMissileRack(5, 6, 0, 0, 360));
        $this->addPrimarySystem(new ReloadRack(5, 9));
        
        $this->addFrontSystem(new Thruster(3, 12, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 12, 0, 3, 1));
        $this->addFrontSystem(new SMissileRack(5, 6, 0, 240, 60));
        $this->addFrontSystem(new RapidGatling(2, 4, 1, 240, 120));
        $this->addFrontSystem(new RapidGatling(2, 4, 1, 240, 120));
        $this->addFrontSystem(new RapidGatling(2, 4, 1, 240, 120));
        $this->addFrontSystem(new SMissileRack(5, 6, 0, 300, 120));

        $this->addAftSystem(new RapidGatling(1, 4, 1, 60, 300));
        $this->addAftSystem(new RapidGatling(1, 4, 1, 60, 300));
        $this->addAftSystem(new Thruster(3, 15, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 3, 2));
		
        $this->addLeftSystem(new Thruster(4, 20, 0, 6, 3));
        $this->addLeftSystem(new CargoBay(2, 25));
        $this->addLeftSystem(new CargoBay(2, 25));
        $this->addLeftSystem(new RapidGatling(2, 4, 1, 180, 360));
        $this->addLeftSystem(new RapidGatling(2, 4, 1, 180, 360));

        $this->addRightSystem(new Thruster(4, 20, 0, 6, 4));
        $this->addRightSystem(new CargoBay(2, 25));
        $this->addRightSystem(new CargoBay(2, 25));
        $this->addRightSystem(new RapidGatling(2, 4, 1, 0, 180));
        $this->addRightSystem(new RapidGatling(2, 4, 1, 0, 180));


		//structures
        $this->addFrontSystem(new Structure(4, 48));
        $this->addAftSystem(new Structure(4, 48));
        $this->addLeftSystem(new Structure(4, 45));
        $this->addRightSystem(new Structure(4, 45));
        $this->addPrimarySystem(new Structure(5, 56));




	//d20 hit chart
	$this->hitChart = array(
		
		//PRIMARY
		0=> array( 
			7 => "Structure",
			8 => "Class-S Missile Rack",
			10 => "Jump Engine",
			12 => "Scanner",
			14 => "Engine",
			15 => "Reload Rack",
			17 => "Hangar",
			18 => "HK Control Node",
			19 => "Reactor",
			20 => "C&C",
		),

		//Forward
		1=> array(
			4 => "Thruster",
			8 => "Class-S Missile Rack",
			11 => "Rapid Gatling Railgun",
			18 => "Structure",
			20 => "Primary",
		),

		//Aft
		2=> array(
			6 => "Thruster",
			9 => "Rapid Gatling Railgun",
			18 => "Structure",
			20 => "Primary",
		),

		//Port
		3=> array(
			4 => "Thruster",
			6 => "Rapid Gatling Railgun",
			11 => "Cargo Bay",
			18 => "Structure",
			20 => "Primary",
		),

		//Starboard
		4=> array(
			4 => "Thruster",
			6 => "Rapid Gatling Railgun",
			11 => "Cargo Bay",
			18 => "Structure",
			20 => "Primary",
		),

	);

        
    }
}
?>
