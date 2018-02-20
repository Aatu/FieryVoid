<?php
class Prophet1780 extends BaseShip{
     /*Orieni Prophet Command Ship, variant ISD 1780 (WoCR)*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 725;
		$this->faction = "Orieni";
        $this->phpclass = "Prophet1780";
        $this->imagePath = "img/ships/prophet.png";
        $this->canvasSize = 280;
        $this->shipClass = "Prophet Command Ship (early)";
        $this->variantOf = "Prophet Command Ship";
	    $this->isd = 1780;
        $this->limited = 33;
        $this->shipSizeClass = 3;
        $this->fighters = array("light"=>12, "medium"=>18, "assault shuttles"=>6);
		
        $this->forwardDefense = 19;
        $this->sideDefense = 19;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 6;
        $this->rollcost = 4;
        $this->pivotcost = 3;
        $this->limited = 33;
        
        $this->addPrimarySystem(new Reactor(5, 34, 0, 0));
        $this->addPrimarySystem(new CnC(6, 30, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 30, 4, 6));
        $this->addPrimarySystem(new Engine(5, 30, 0, 8, 4));
        $this->addPrimarySystem(new Hangar(5, 38, 30));
        $this->addPrimarySystem(new JumpEngine(5, 40, 6, 25));
        $this->addPrimarySystem(new HKControlNode(5, 24, 3, 3));
        
        $this->addFrontSystem(new SoMissileRack(5, 6, 0, 270, 90));
        $this->addFrontSystem(new SoMissileRack(5, 6, 0, 270, 90));
        $this->addFrontSystem(new GaussCannon(2, 10, 4, 270, 90));
        $this->addFrontSystem(new OrieniGatlingRG(1, 4, 1, 240, 120));
        $this->addFrontSystem(new OrieniGatlingRG(1, 4, 1, 240, 120));
        $this->addFrontSystem(new OrieniGatlingRG(1, 4, 1, 240, 120));
        $this->addFrontSystem(new CargoBay(2, 25));
        $this->addFrontSystem(new Thruster(2, 15, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 15, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 15, 0, 2, 1));
        
	$this->addAftSystem(new SoMissileRack(5, 6, 0, 90, 270));
        $this->addAftSystem(new GaussCannon(4, 10, 4, 90, 270));
        $this->addAftSystem(new OrieniGatlingRG(1, 4, 1, 60, 300));
        $this->addAftSystem(new OrieniGatlingRG(1, 4, 1, 60, 300));
        $this->addAftSystem(new OrieniGatlingRG(1, 4, 1, 60, 300));
        $this->addAftSystem(new CargoBay(2, 25));
        $this->addAftSystem(new Thruster(2, 15, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 15, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 15, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 15, 0, 2, 2));
		
        $this->addLeftSystem(new SoMissileRack(5, 6, 0, 240, 60));
        $this->addLeftSystem(new GaussCannon(2, 10, 4, 180, 360));
        $this->addLeftSystem(new LaserLance(2, 6, 4, 180, 360));
        $this->addLeftSystem(new LaserLance(2, 6, 4, 180, 360));
        $this->addLeftSystem(new OrieniGatlingRG(1, 4, 1, 180, 360));
        $this->addLeftSystem(new OrieniGatlingRG(1, 4, 1, 180, 360));
        $this->addLeftSystem(new OrieniGatlingRG(1, 4, 1, 180, 360));
        $this->addLeftSystem(new CargoBay(2, 25));
        $this->addLeftSystem(new Thruster(4, 25, 0, 6, 3));

        $this->addRightSystem(new SoMissileRack(5, 6, 0, 300, 120));
        $this->addRightSystem(new GaussCannon(2, 10, 4, 0, 180));    
        $this->addRightSystem(new LaserLance(2, 6, 4, 0, 180));
        $this->addRightSystem(new LaserLance(2, 6, 4, 0, 180));
        $this->addRightSystem(new OrieniGatlingRG(1, 4, 1, 0, 180));
        $this->addRightSystem(new OrieniGatlingRG(1, 4, 1, 0, 180));
        $this->addRightSystem(new OrieniGatlingRG(1, 4, 1, 0, 180));
        $this->addRightSystem(new CargoBay(2, 25));    
	$this->addRightSystem(new Thruster(4, 25, 0, 6, 4));
		
	//structures
        $this->addFrontSystem(new Structure(4, 60));
        $this->addAftSystem(new Structure(4, 60));
        $this->addLeftSystem(new Structure(4, 68));
        $this->addRightSystem(new Structure(4, 68));
        $this->addPrimarySystem(new Structure(5, 60));
        


	//d20 hit chart
	$this->hitChart = array(
		
		//PRIMARY
		0=> array( 
			7 => "Structure",
			9 => "Jump Engine",
			11 => "Scanner",
			13 => "Engine",
			16 => "Hangar",
			18 => "HK Control Node",
			19 => "Reactor",
			20 => "C&C",
		),
		//Forward
		1=> array(
			3 => "Thruster",
			5 => "Class-SO Missile Rack",
			7 => "Gauss Cannon",
			9 => "Gatling Railgun",
			12 => "Cargo Bay",
			18 => "Structure",
			20 => "Primary",
		),
		//Aft
		2=> array(
			5 => "Thruster",
			6 => "Class-SO Missile Rack",
			8 => "Gauss Cannon",
			10 => "Gatling Railgun",
			12 => "Cargo Bay",
			18 => "Structure",
			20 => "Primary",
		),
		//Port
		3=> array(
			3 => "Thruster",
			5 => "Laser Lance",
			7 => "Gauss Cannon",
			8 => "Class-SO Missile Rack",
			10 => "Gatling Railgun",
			12 => "Cargo Bay",
			18 => "Structure",
			20 => "Primary",
		),
		//Starboard
		4=> array(
			3 => "Thruster",
			5 => "Laser Lance",
			7 => "Gauss Cannon",
			8 => "Class-SO Missile Rack",
			10 => "Gatling Railgun",
			12 => "Cargo Bay",
			18 => "Structure",
			20 => "Primary",
		),
	);



    }
}
