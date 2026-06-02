<?php
class vengeanceGC extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 490;
		$this->faction = "Great Crusade Orieni Imperium";
        $this->phpclass = "vengeanceGC";
        $this->imagePath = "img/ships/GCvengeance.png";
        $this->shipClass = "Vengeance Attack Frigate";
        $this->agile = true;
        $this->canvasSize = 110;
	    $this->isd = 2240;

		$this->unofficial = true;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
		$this->iniativebonus = 65;
         
        $this->addPrimarySystem(new Reactor(4, 15, 0, 0));
        $this->addPrimarySystem(new CnC(4, 15, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 3, 7));
        $this->addPrimarySystem(new Engine(3, 15, 0, 12, 2));
		$this->addPrimarySystem(new Hangar(1, 1, 1));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 5, 3));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 5, 4));        
	
        $this->addFrontSystem(new GaussRifle(3, 8, 4, 300, 60));
        $this->addFrontSystem(new GaussRifle(3, 8, 4, 300, 60));
		$this->addFrontSystem(new ImpRapidGatling(2, 4, 2, 180, 60));
        $this->addFrontSystem(new ImpRapidGatling(2, 4, 2, 300, 180));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));

        $this->addAftSystem(new LightLaserLance(2, 6, 5, 180, 360));
        $this->addAftSystem(new LightLaserLance(2, 6, 5, 0, 180));    
        $this->addAftSystem(new ImpRapidGatling(2, 4, 2, 120, 360));
        $this->addAftSystem(new ImpRapidGatling(2, 4, 2, 0, 240));
        $this->addAftSystem(new Thruster(2, 6, 0, 4, 2));
        $this->addAftSystem(new Thruster(2, 8, 0, 4, 2));
        $this->addAftSystem(new Thruster(2, 6, 0, 4, 2));    
       
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
			8 => "Gauss Rifle",
			11 => "Improved Gatling Railgun",
			17 => "Structure",
			20 => "Primary",
		),
		2=> array(
			6 => "Thruster",
			8 => "Light Laser Lance",
			10 => "Improved Gatling Railgun",
			17 => "Structure",
			20 => "Primary",
		),
	);

			
        }
    }
?>
