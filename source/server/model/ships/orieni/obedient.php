<?php
class Obedient extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 310;
        $this->faction = "Orieni";
        $this->phpclass = "Obedient";
        $this->imagePath = "img/ships/obedient.png";
        $this->shipClass = "Obedient Patrol Frigate";
        $this->agile = true;
        $this->canvasSize = 100;
	    
	$this->isd = 2007;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 11;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus = 60;
        
         
        $this->addPrimarySystem(new Reactor(4, 12, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 11, 2, 5));
        $this->addPrimarySystem(new Engine(3, 16, 0, 12, 2));
        $this->addPrimarySystem(new Hangar(1, 1));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 6, 3));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 6, 4));        
        
        $this->addFrontSystem(new GaussCannon(3, 10, 4, 300, 60));  
        $this->addFrontSystem(new LightLaser(1, 4, 3, 270, 90));
        $this->addFrontSystem(new RapidGatling(2, 4, 1, 240, 120));
        $this->addFrontSystem(new Thruster(2, 7, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 7, 0, 3, 1));
 
        $this->addAftSystem(new RapidGatling(1, 4, 1, 120, 360));
        $this->addAftSystem(new RapidGatling(1, 4, 1, 0, 240));
        $this->addAftSystem(new Thruster(2, 6, 0, 4, 2));
        $this->addAftSystem(new Thruster(2, 6, 0, 4, 2));
        $this->addAftSystem(new Thruster(2, 6, 0, 4, 2));   
       
        $this->addPrimarySystem(new Structure(4, 36));
        
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
			7 => "Gauss Cannon",
			8 => "Light Laser",
			9 => "Rapid Gatling Railgun",
			17 => "Structure",
			20 => "Primary",
		),
		2=> array(
			7 => "Thruster",
			9 => "Rapid Gatling Railgun",
			17 => "Structure",
			20 => "Primary",
		),
	);
        
        
        
        }
    }
?>

        
