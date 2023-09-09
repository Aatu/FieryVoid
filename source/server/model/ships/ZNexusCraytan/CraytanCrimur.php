<?php
class CraytanCrimur extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 600;
	$this->faction = "ZNexus Craytan Union";
        $this->phpclass = "CraytanCrimur";
        $this->imagePath = "img/ships/Nexus/CraytanDakran.png";
        $this->shipClass = "Crimur Jump Carrier";
			$this->variantOf = "Dakran Heavy Cruiser";
			$this->occurence = "common";
        $this->shipSizeClass = 3;
		$this->canvasSize = 170; 
		$this->unofficial = true;
        $this->limited = 10;

        $this->fighters = array("normal"=>6, "assult shuttles"=>2);

		$this->isd = 2136;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 0.75;
        $this->turndelaycost = 0.75;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(5, 20, 0, 0));
        $this->addPrimarySystem(new CnC(5, 19, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 4, 7));
        $this->addPrimarySystem(new Engine(4, 20, 0, 8, 3));
		$this->addPrimarySystem(new Hangar(4, 8));
		$this->addPrimarySystem(new CargoBay(4, 14));
		
        $this->addFrontSystem(new Thruster(4, 9, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 9, 0, 4, 1));
		$this->addFrontSystem(new NexusHeavyEnhPlasma(3, 9, 5, 240, 360));
		$this->addFrontSystem(new NexusLightEnhPlasma(2, 5, 2, 240, 60));
		$this->addFrontSystem(new NexusACIDS(3, 6, 2, 270, 90));
		$this->addFrontSystem(new NexusLightEnhPlasma(2, 5, 2, 300, 120));
		$this->addFrontSystem(new NexusHeavyEnhPlasma(3, 9, 5, 0, 120));

        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
		$this->addAftSystem(new NexusLightEnhPlasma(3, 5, 2, 180, 300));
		$this->addAftSystem(new NexusACIDS(3, 6, 2, 90, 270));
		$this->addAftSystem(new NexusLightEnhPlasma(3, 5, 2, 60, 180));
		$this->addAftSystem(new JumpEngine(3, 12, 8, 50));

        $this->addLeftSystem(new NexusAssaultCannon(4, 8, 5, 300, 360));
		$this->addLeftSystem(new NexusMedEnhPlasma(3, 6, 4, 180, 360));
		$this->addLeftSystem(new NexusACIDS(3, 6, 2, 180, 360));
        $this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));

        $this->addRightSystem(new NexusAssaultCannon(4, 8, 5, 0, 60));
		$this->addRightSystem(new NexusMedEnhPlasma(3, 6, 4, 0, 180));
		$this->addRightSystem(new NexusACIDS(3, 6, 2, 0, 180));
        $this->addRightSystem(new Thruster(4, 15, 0, 5, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 48));
        $this->addAftSystem(new Structure(4, 40));
        $this->addLeftSystem(new Structure(4, 48));
        $this->addRightSystem(new Structure(4, 48));
        $this->addPrimarySystem(new Structure(5, 48));
		
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
					7 => "Light Enhanced Plasma",
					8 => "Advanced Close-In Defense System",
					10 => "Heavy Enhanced Plasma",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					5 => "Thruster",
					6 => "Advanced Close-In Defense System",
					8 => "Light Enhanced Plasma",
					10 => "Jump Engine",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					5 => "Thruster",
					6 => "Advanced Close-In Defense System",
					8 => "Medium Enhanced Plasma",
					10 => "Assault Cannon",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					6 => "Advanced Close-In Defense System",
					8 => "Medium Enhanced Plasma",
					10 => "Assault Cannon",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
