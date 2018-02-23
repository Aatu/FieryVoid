<?php
class Pariah extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 790;
		$this->faction = "Orieni";
        $this->phpclass = "Pariah";
        $this->imagePath = "img/ships/enlightenment.png";
        $this->shipClass = "Pariah Light Command Ship";
        $this->variantOf = "Enlightenment Invader";
        $this->occurence = "uncommon";
	    $this->isd = 2009;
	    
        $this->shipSizeClass = 3;
        $this->fighters = array("light"=>24, "medium"=>12); //PRIMARY hangar for 12 fighters (can hold HKs) and side hangards for 12 light fighters each
        $this->canvasSize = 200;
		
        $this->forwardDefense = 19;
        $this->sideDefense = 18;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1;
        $this->accelcost = 5;
        $this->rollcost = 3;
        $this->pivotcost = 3;


        
        $this->addPrimarySystem(new Reactor(5, 36, 0, 0));
        $this->addPrimarySystem(new CnC(5, 25, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 25, 4, 7));
        $this->addPrimarySystem(new Engine(5, 25, 0, 8, 4));
        $this->addPrimarySystem(new HKControlNode(5, 12, 1, 1));
        $this->addPrimarySystem(new Hangar(4, 15, 6));        
           
        $this->addFrontSystem(new HeavyLaserLance(2, 6, 4, 240, 60));
        $this->addFrontSystem(new HeavyLaserLance(2, 6, 4, 300, 120));
        $this->addFrontSystem(new RapidGatling(2, 4, 1, 240, 120));
        $this->addFrontSystem(new RapidGatling(2, 4, 1, 240, 120));
        $this->addFrontSystem(new RapidGatling(2, 4, 1, 240, 120));
        $this->addFrontSystem(new Thruster(2, 15, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 15, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 15, 0, 2, 1));     

        $this->addAftSystem(new RapidGatling(1, 4, 1, 60, 300));
        $this->addAftSystem(new RapidGatling(1, 4, 1, 60, 300));
        $this->addAftSystem(new Thruster(3, 12, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 2, 2));

        $this->addLeftSystem(new HeavyGaussCannon(3, 10, 4, 240, 360));
        $this->addLeftSystem(new SMissileRack(3, 6, 0, 240, 360));
        $this->addLeftSystem(new SMissileRack(3, 6, 0, 180, 300));
        $this->addLeftSystem(new RapidGatling(2, 4, 1, 180, 360));
        $this->addLeftSystem(new RapidGatling(2, 4, 1, 180, 360));
        $this->addLeftSystem(new Hangar(3, 12, 6));		
        $this->addLeftSystem(new Thruster(4, 25, 0, 6, 3));
 
        $this->addRightSystem(new HeavyGaussCannon(3, 10, 4, 0, 120));
        $this->addRightSystem(new SMissileRack(3, 6, 0, 0, 120));
        $this->addRightSystem(new SMissileRack(3, 6, 0, 60, 180));
        $this->addRightSystem(new RapidGatling(2, 4, 1, 0, 180));
        $this->addRightSystem(new RapidGatling(2, 4, 1, 0, 180));
        $this->addRightSystem(new Hangar(3, 12, 6));    
        $this->addRightSystem(new Thruster(4, 25, 0, 6, 4));

		//structures
        $this->addFrontSystem(new Structure(4, 51));
        $this->addAftSystem(new Structure(4, 54));
        $this->addLeftSystem(new Structure(4, 60));
        $this->addRightSystem(new Structure(4, 60));
        $this->addPrimarySystem(new Structure(5, 48));
        
	    

	//d20 hit chart
	$this->hitChart = array(
		
		//PRIMARY
		0=> array( 
			9 => "Structure",
			11 => "Scanner",
			13 => "Engine",
			16 => "Hangar",
			18 => "HK Control Node",	
			19 => "Reactor",
			20 => "C&C",
		),
		//Forward
		1=> array(
			6 => "Thruster",
			8 => "Heavy Laser Lance",
			11 => "Rapid Gatling Railgun",
			18 => "Structure",
			20 => "Primary",
		),
		//Aft
		2=> array(
			8 => "Thruster",
			10 => "Rapid Gatling Railgun",
			18 => "Structure",
			20 => "Primary",
		),
		//Port
		3=> array(
			4 => "Thruster",
			6 => "Class-SO Missile Rack",
			8 => "Rapid Gatling Railgun",
			10 => "Hangar",			
			12 => "Cargo Bay",
			18 => "Structure",
			20 => "Primary",
		),
		//Starboard
		4=> array(
			4 => "Thruster",
			6 => "Class-S Missile Rack",
			8 => "Heavy Gauss Cannon",
			10 => "Rapid Gatling Railgun",
			12 => "Hangar",	
			18 => "Structure",
			20 => "Primary",
		),
	);
	    
	    
    }

}



?>
