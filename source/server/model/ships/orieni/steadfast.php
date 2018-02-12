<?php
class Steadfast extends MediumShip{
    /*Orieni Steadfast Escort Corvette, variant ISD 2007*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 445;
		$this->faction = "Orieni";
        $this->phpclass = "Steadfast";
        $this->imagePath = "img/ships/steadfast.png";
        $this->shipClass = "Steadfast Escort Corvette";
        $this->agile = true;
        $this->canvasSize = 100;
	    $this->isd = 2007;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
		$this->iniativebonus = 60;
        
         
        $this->addPrimarySystem(new Reactor(4, 15, 0, 0));
        $this->addPrimarySystem(new CnC(4, 15, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 3, 6));
        $this->addPrimarySystem(new Engine(3, 15, 0, 10, 2));
		$this->addPrimarySystem(new Hangar(1, 1));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 5, 3));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 5, 4));        

	
        $this->addFrontSystem(new GaussCannon(3, 10, 4, 300, 60));
        $this->addFrontSystem(new GaussCannon(3, 10, 4, 300, 60));
	$this->addFrontSystem(new RapidGatling(2, 4, 1, 180, 60));
        $this->addFrontSystem(new RapidGatling(2, 4, 1, 300, 180));
        $this->addFrontSystem(new Thruster(1, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(1, 6, 0, 2, 1));



        $this->addAftSystem(new LightLaser(1, 4, 3, 180, 360));
        $this->addAftSystem(new LightLaser(1, 4, 3, 0, 180));    
        $this->addAftSystem(new RapidGatling(1, 4, 1, 120, 360));
        $this->addAftSystem(new RapidGatling(1, 4, 1, 0, 240));
        $this->addAftSystem(new Thruster(2, 6, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 8, 0, 4, 2));
        $this->addAftSystem(new Thruster(2, 6, 0, 3, 2));    
        
       
        $this->addPrimarySystem(new Structure(4, 46));
		


	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			8 => "Thruster",
			11 => "Scanner",
			15 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),
		1=> array(
			5 => "Thruster",
			8 => "Gauss Cannon",
			11 => "Rapid Gatling Railgun",
			17 => "Structure",
			20 => "Primary",
		),
		2=> array(
			6 => "Thruster",
			8 => "Light Laser",
			10 => "Rapid Gatling Railgun",
			17 => "Structure",
			20 => "Primary",
		),
	);

			
        }
    }
?>
