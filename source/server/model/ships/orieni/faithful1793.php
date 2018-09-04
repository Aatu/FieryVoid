<?php
class Faithful1793 extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 575;
		$this->faction = "Orieni";
        $this->phpclass = "Faithful1793";
        $this->imagePath = "img/ships/faithful.png";
        $this->canvasSize = 200;
        $this->shipClass = "Faithful Search Explorer (early)";
        $this->variantOf = "Faithful Search Explorer";
	    $this->isd = 1793;
        $this->shipSizeClass = 3;
	    
        $this->fighters = array("light"=>12);
		
        $this->forwardDefense = 18;
        $this->sideDefense = 18;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->limited = 10;
        $this->addPrimarySystem(new Reactor(5, 30, 0, 0));
        $this->addPrimarySystem(new CnC(6, 30, 0, 0));
        $this->addPrimarySystem(new ElintScanner(5, 25, 5, 6));
        $this->addPrimarySystem(new Engine(5, 25, 0, 8, 4));
        $this->addPrimarySystem(new Hangar(4, 15, 12));
        $this->addPrimarySystem(new JumpEngine(5, 35, 6, 25));
        $this->addPrimarySystem(new CargoBay(4, 25));
               
        $this->addFrontSystem(new LaserLance(2, 6, 4, 240, 60));
        $this->addFrontSystem(new OrieniGatlingRG(1, 4, 1, 240, 120));
        $this->addFrontSystem(new OrieniGatlingRG(1, 4, 1, 240, 120));
        $this->addFrontSystem(new Thruster(2, 12, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 12, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 12, 0, 2, 1)); 
	    
        $this->addAftSystem(new OrieniGatlingRG(1, 4, 1, 60, 300));
        $this->addAftSystem(new OrieniGatlingRG(1, 4, 1, 60, 300));
        $this->addAftSystem(new Thruster(2, 15, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 15, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 15, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 15, 0, 3, 2));
        
        $this->addLeftSystem(new OrieniGatlingRG(1, 4, 1, 180, 360));
        $this->addLeftSystem(new OrieniGatlingRG(1, 4, 1, 180, 360));
        $this->addLeftSystem(new LaserLance(2, 6, 4, 180, 360));
        $this->addLeftSystem(new CargoBay(2, 25));
        $this->addLeftSystem(new Thruster(4, 23, 0, 6, 3));
	    
        $this->addRightSystem(new OrieniGatlingRG(1, 4, 1, 0, 180));
        $this->addRightSystem(new OrieniGatlingRG(1, 4, 1, 0, 180));
        $this->addRightSystem(new LaserLance(2, 6, 4, 0, 180));
        $this->addRightSystem(new CargoBay(2, 25));
        $this->addRightSystem(new Thruster(4, 23, 0, 6, 4));
	    
	    
		//structures
        $this->addFrontSystem(new Structure(4, 48));
        $this->addAftSystem(new Structure(4, 48));
        $this->addLeftSystem(new Structure(4, 45));
        $this->addRightSystem(new Structure(4, 45));
        $this->addPrimarySystem(new Structure(5, 60));
	    
	//d20 hit chart
	$this->hitChart = array(
		
		//PRIMARY
		0=> array( 
			8 => "Structure",
			10 => "Jump Engine",
			12 => "ELINT Scanner",
			14 => "Engine",
			16 => "Hangar",
			18 => "Cargo Bay",
			19 => "Reactor",
			20 => "C&C",
		),
		//Forward
		1=> array(
			6 => "Thruster",
			8 => "Laser Lance",
			10 => "Gatling Railgun",
			18 => "Structure",
			20 => "Primary",
		),
		//Aft
		2=> array(
			8 => "Thruster",
			10 => "Gatling Railgun",
			18 => "Structure",
			20 => "Primary",
		),
		//Port
		3=> array(
			5 => "Thruster",
			7 => "Laser Lance",
			9 => "Gatling Railgun",
			12 => "Cargo Bay",
			18 => "Structure",
			20 => "Primary",
		),
		//Starboard
		4=> array(
			5 => "Thruster",
			7 => "Laser Lance",
			9 => "Gatling Railgun",
			12 => "Cargo Bay",
			18 => "Structure",
			20 => "Primary",
		),
	);	    
	    
	    
    }
}
?>
