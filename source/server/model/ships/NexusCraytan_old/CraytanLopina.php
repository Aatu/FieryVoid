<?php
class CraytanLopina extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 490;
	$this->faction = "Nexus Craytan Union (early)";
        $this->phpclass = "CraytanLopina";
        $this->imagePath = "img/ships/Nexus/craytan_lopin.png";
        $this->shipClass = "Lopin Explorer";
        $this->shipSizeClass = 3;
		$this->canvasSize = 160; 
		$this->unofficial = true;
        $this->limited = 10;

        $this->fighters = array("assault shuttles"=>6);

		$this->isd = 2100;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 16;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(4, 20, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new ElintScanner(4, 16, 7, 7));
        $this->addPrimarySystem(new Engine(4, 18, 0, 8, 3));
		$this->addPrimarySystem(new Hangar(4, 6));
		$this->addPrimarySystem(new Magazine(4, 9));
		
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
		$this->addFrontSystem(new LightPlasma(2, 4, 2, 240, 60));
		$this->addFrontSystem(new HeavyPlasma(3, 8, 5, 240, 360));
		$this->addFrontSystem(new CargoBay(3, 16));
		$this->addFrontSystem(new HeavyPlasma(3, 8, 5, 0, 120));
		$this->addFrontSystem(new LightPlasma(2, 4, 2, 300, 120));

        $this->addAftSystem(new Thruster(3, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 2, 2));
		$this->addAftSystem(new JumpEngine(3, 12, 8, 50));

        $this->addLeftSystem(new CargoBay(3, 20));
		$this->addLeftSystem(new NexusCIDS(2, 4, 2, 180, 360));
        $this->addLeftSystem(new NexusCIDS(2, 4, 2, 180, 360));
		$this->addLeftSystem(new MediumPlasma(2, 5, 3, 180, 360));
        $this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));

        $this->addRightSystem(new CargoBay(3, 20));
		$this->addRightSystem(new NexusCIDS(2, 4, 2, 0, 180));
        $this->addRightSystem(new NexusCIDS(2, 4, 2, 0, 180));
		$this->addRightSystem(new MediumPlasma(2, 5, 3, 0, 180));
        $this->addRightSystem(new Thruster(4, 15, 0, 5, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 48));
        $this->addAftSystem(new Structure(4, 48));
        $this->addLeftSystem(new Structure(4, 52));
        $this->addRightSystem(new Structure(4, 52));
        $this->addPrimarySystem(new Structure(4, 54));
		
		$this->hitChart = array(
			0=> array(
					9 => "Structure",
					10 => "Magazine",
					13 => "ELINT Scanner",
					16 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					5 => "Thruster",
					7 => "Heavy Plasma Cannon",
					9 => "Light Plasma Cannon",
					11 => "Cargo Bay",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					8 => "Thruster",
					10 => "Jump Engine",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					6 => "Cargo Bay",
					8 => "Medium Plasma Cannon",
					10 => "Close-In Defense System",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					6 => "Cargo Bay",
					8 => "Medium Plasma Cannon",
					10 => "Close-In Defense System",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
