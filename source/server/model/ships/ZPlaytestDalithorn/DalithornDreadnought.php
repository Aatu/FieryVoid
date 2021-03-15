<?php
class DalithornDreadnought extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 450;
	$this->faction = "ZPlaytest Dalithorn";
        $this->phpclass = "DalithornDreadnought";
        $this->imagePath = "img/ships/Nexus/DalithornDreadnought.png";
        $this->shipClass = "Dreadnought";
        $this->shipSizeClass = 3;
		$this->canvasSize = 175; //img has 200px per side
		$this->unofficial = true;
        $this->limited = 33;

        $this->fighters = array("superheavy"=>2);

		$this->isd = 2050;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(4, 19, 0, 0));
        $this->addPrimarySystem(new CnC(4, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 16, 5, 5));
        $this->addPrimarySystem(new Engine(3, 18, 0, 6, 3));
		$this->addPrimarySystem(new Hangar(1, 2));
		$this->addPrimarySystem(new CargoBay(4, 16));
		
        $this->addFrontSystem(new Thruster(2, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 10, 0, 3, 1));
		$this->addFrontSystem(new NexusShatterGun(1, 2, 1, 270, 90));
		$this->addFrontSystem(new NexusShatterGun(1, 2, 1, 270, 90));
		$this->addFrontSystem(new NexusLightGasGun(2, 5, 1, 240, 360));
		$this->addFrontSystem(new NexusLightGasGun(2, 5, 1, 0, 120));
		$this->addFrontSystem(new NexusCoilgun(2, 10, 4, 330, 30));
		$this->addFrontSystem(new NexusCoilgun(2, 10, 4, 330, 30));

        $this->addAftSystem(new Thruster(2, 10, 0, 1, 2));
        $this->addAftSystem(new Thruster(2, 10, 0, 1, 2));
        $this->addAftSystem(new Thruster(3, 14, 0, 4, 2));
		$this->addAftSystem(new NexusLightGasGun(2, 5, 1, 180, 300));
		$this->addAftSystem(new NexusLightGasGun(2, 5, 1, 60, 180));
        $this->addAftSystem(new Catapult(1, 6));
        $this->addAftSystem(new Catapult(1, 6));

        $this->addLeftSystem(new NexusMediumChemicalLaser(2, 7, 2, 300, 360));
        $this->addLeftSystem(new NexusGasGun(2, 7, 2, 180, 360));
		$this->addLeftSystem(new NexusShatterGun(1, 2, 1, 180, 360));
        $this->addLeftSystem(new Thruster(2, 14, 0, 3, 3));

        $this->addRightSystem(new NexusMediumChemicalLaser(2, 7, 2, 0, 60));
        $this->addRightSystem(new NexusGasGun(2, 7, 2, 0, 180));
		$this->addRightSystem(new NexusShatterGun(1, 2, 1, 0, 180));
        $this->addRightSystem(new Thruster(2, 14, 0, 3, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 38));
        $this->addAftSystem(new Structure(4, 32));
        $this->addLeftSystem(new Structure(4, 35));
        $this->addRightSystem(new Structure(4, 35));
        $this->addPrimarySystem(new Structure(4, 40));
		
		$this->hitChart = array(
			0=> array(
					9 => "Structure",
					11 => "Cargo Bay",
					13 => "Scanner",
					16 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					5 => "Thruster",
					7 => "Light Gas Gun",
					11 => "Coilgun",
					12 => "Shatter Gun",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Catapult",
					10 => "Light Gas Gun",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					6 => "Medium Chemical Laser",
					8 => "Gas Gun",
					10 => "Shatter Gun",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					6 => "Medium Chemical Laser",
					8 => "Gas Gun",
					10 => "Shatter Gun",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
