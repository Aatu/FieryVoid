<?php
class obedientGC extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 340;
        $this->faction = "Great Crusade Orieni Imperium";
        $this->phpclass = "obedientGC";
        $this->imagePath = "img/ships/GCobedient.png";
        $this->shipClass = "Obedient Patrol Frigate (2228)";
        $this->agile = true;
        $this->canvasSize = 100;

		$this->unofficial = true;
	    
		$this->isd = 2228;
        
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
        $this->addPrimarySystem(new Scanner(3, 11, 2, 6));
        $this->addPrimarySystem(new Engine(3, 16, 0, 12, 2));
        $this->addPrimarySystem(new Hangar(1, 1, 1));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 6, 3));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 6, 4));        
        
        $this->addFrontSystem(new GaussRifle(3, 8, 4, 300, 60));  
        $this->addFrontSystem(new LightLaser(1, 4, 3, 270, 90));
        $this->addFrontSystem(new ImpRapidGatling(2, 4, 2, 240, 120));
        $this->addFrontSystem(new Thruster(2, 7, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 7, 0, 3, 1));
 
        $this->addAftSystem(new ImpRapidGatling(2, 4, 2, 120, 360));
        $this->addAftSystem(new ImpRapidGatling(2, 4, 2, 0, 240));
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
			7 => "Gauss Rifle",
			8 => "Light Laser",
			9 => "Improved Gatling Railgun",
			17 => "Structure",
			20 => "Primary",
		),
		2=> array(
			7 => "Thruster",
			9 => "Improved Gatling Railgun",
			17 => "Structure",
			20 => "Primary",
		),
	);
        
        }
    }
?>

        