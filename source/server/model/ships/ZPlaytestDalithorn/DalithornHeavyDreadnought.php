<?php
class DalithornHeavyDreadnought extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 480;
	$this->faction = "ZPlaytest Dalithorn";
        $this->phpclass = "DalithornHeavyDreadnought";
        $this->imagePath = "img/ships/Playtest/DalithornDreadnought.png";
        $this->shipClass = "Heavy Dreadnought";
		$this->shipSizeClass = 3;
		$this->canvasSize = 175; //img has 200px per side
		$this->unofficial = true;

        $this->fighters = array("superheavy"=>2);

		$this->isd = 2098;
        
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
        $this->addPrimarySystem(new Engine(3, 18, 0, 8, 3));
		$this->addPrimarySystem(new Hangar(1, 2));
		$this->addPrimarySystem(new CargoBay(4, 16));
		
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
		$this->addFrontSystem(new NexusMinigun(2, 4, 1, 240, 60));
		$this->addFrontSystem(new NexusMinigun(2, 4, 1, 300, 120));
		$this->addFrontSystem(new NexusAutocannon(3, 4, 1, 240, 60));
		$this->addFrontSystem(new NexusAutocannon(3, 4, 1, 300, 120));
		$this->addFrontSystem(new NexusCoilgun(2, 10, 4, 330, 30));
		$this->addFrontSystem(new NexusCoilgun(2, 10, 4, 330, 30));

        $this->addAftSystem(new Thruster(3, 10, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 14, 0, 4, 2));
		$this->addAftSystem(new NexusAutocannon(3, 4, 1, 120, 300));
		$this->addAftSystem(new NexusAutocannon(3, 4, 1, 60, 240));
		$this->addAftSystem(new NexusMinigun(2, 4, 1, 120, 300));
		$this->addAftSystem(new NexusMinigun(2, 4, 1, 60, 240));
        $this->addAftSystem(new Catapult(1, 6));
        $this->addAftSystem(new Catapult(1, 6));

        $this->addLeftSystem(new NexusMediumChemicalLaser(3, 7, 2, 300, 360));
        $this->addLeftSystem(new NexusGasGun(2, 7, 2, 180, 360));
		$this->addLeftSystem(new NexusProtector(2, 4, 1, 180, 360));
        $this->addLeftSystem(new Thruster(3, 14, 0, 4, 3));

        $this->addRightSystem(new NexusMediumChemicalLaser(3, 7, 2, 0, 60));
        $this->addRightSystem(new NexusGasGun(2, 7, 2, 0, 180));
		$this->addRightSystem(new NexusProtector(2, 4, 1, 0, 180));
        $this->addRightSystem(new Thruster(3, 14, 0, 4, 4));

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
					6 => "Minigun",
					10 => "Coilgun",
					12 => "Autocannon",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Catapult",
					10 => "Autocannon",
					11 => "Minigun",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					6 => "Thruster",
					8 => "Medium Chemical Laser",
					10 => "Gas Gun",
					11 => "Protector",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					6 => "Thruster",
					8 => "Medium Chemical Laser",
					10 => "Gas Gun",
					11 => "Protector",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
