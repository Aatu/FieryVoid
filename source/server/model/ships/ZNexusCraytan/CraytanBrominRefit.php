<?php
class CraytanBrominRefit extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 590;
	$this->faction = "ZNexus Craytan Union";
        $this->phpclass = "CraytanBrominRefit";
        $this->imagePath = "img/ships/Nexus/CraytanEpiron.png";
        $this->shipClass = "Bromin Jump Carrier";
			$this->variantOf = "Epiron Cruiser";
			$this->occurence = "common";
        $this->shipSizeClass = 3;
		$this->canvasSize = 160; 
		$this->unofficial = true;
        $this->limited = 10;

        $this->fighters = array("normal"=>6, "assault shuttle"=>2);

		$this->isd = 2120;
        
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
        $this->addPrimarySystem(new Scanner(4, 16, 4, 6));
        $this->addPrimarySystem(new Engine(4, 18, 0, 8, 3));
		$this->addPrimarySystem(new Hangar(4, 8));
		$this->addPrimarySystem(new CargoBay(4, 7));
		
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
		$this->addFrontSystem(new NexusLightEnhPlasma(2, 5, 2, 240, 60));
		$this->addFrontSystem(new NexusHeavyEnhPlasma(3, 9, 5, 240, 360));
		$this->addFrontSystem(new NexusCIDS(2, 4, 2, 300, 60));
		$this->addFrontSystem(new NexusHeavyEnhPlasma(3, 9, 5, 0, 120));
		$this->addFrontSystem(new NexusLightEnhPlasma(2, 5, 2, 300, 120));

        $this->addAftSystem(new Thruster(3, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 2, 2));
		$this->addAftSystem(new JumpEngine(3, 12, 8, 50));
        $this->addAftSystem(new Thruster(3, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 2, 2));

        $this->addLeftSystem(new NexusAssaultCannon(4, 8, 5, 300, 360));
		$this->addLeftSystem(new NexusCIDS(2, 4, 2, 180, 360));
        $this->addLeftSystem(new NexusCIDS(2, 4, 2, 180, 360));
        $this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));

        $this->addRightSystem(new NexusAssaultCannon(4, 8, 5, 0, 60));
		$this->addRightSystem(new NexusCIDS(2, 4, 2, 0, 180));
        $this->addRightSystem(new NexusCIDS(2, 4, 2, 0, 180));
        $this->addRightSystem(new Thruster(4, 15, 0, 5, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 48));
        $this->addAftSystem(new Structure(4, 42));
        $this->addLeftSystem(new Structure(4, 48));
        $this->addRightSystem(new Structure(4, 48));
        $this->addPrimarySystem(new Structure(4, 54));
		
		$this->hitChart = array(
			0=> array(
					9 => "Structure",
					10 => "Cargo Bay",
					13 => "Scanner",
					16 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					5 => "Thruster",
					8 => "Heavy Enhanced Plasma",
					10 => "Light Enhanced Plasma",
					11 => "Close-In Defense System",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					7 => "Thruster",
					10 => "Jump Engine",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					5 => "Thruster",
					7 => "Close-In Defense System",
					9 => "Assault Cannon",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					7 => "Close-In Defense System",
					9 => "Assault Cannon",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
