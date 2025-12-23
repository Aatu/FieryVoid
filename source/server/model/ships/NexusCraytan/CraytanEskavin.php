<?php
class CraytanEskavin extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 625;
	$this->faction = "Nexus Craytan Union";
        $this->phpclass = "CraytanEskavin";
        $this->imagePath = "img/ships/Nexus/craytan_dakran.png";
        $this->shipClass = "Eskavin Gun Cruiser";
			$this->variantOf = "Dakran Heavy Cruiser";
			$this->occurence = "uncommon";
        $this->shipSizeClass = 3;
		$this->canvasSize = 160; 
		$this->unofficial = true;

        $this->fighters = array("assault shuttles"=>6);

		$this->isd = 2131;
        
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
		$this->addPrimarySystem(new Hangar(4, 6));
		$this->addPrimarySystem(new Magazine(4, 16));
		
        $this->addFrontSystem(new Thruster(4, 9, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 9, 0, 4, 1));
        $this->addFrontSystem(new NexusAssaultCannon(4, 8, 5, 300, 360));
		$this->addFrontSystem(new NexusACIDS(3, 6, 2, 270, 90));
        $this->addFrontSystem(new NexusAssaultCannon(4, 8, 5, 0, 60));

        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
		$this->addAftSystem(new NexusMedEnhPlasma(3, 6, 4, 180, 300));
		$this->addAftSystem(new NexusACIDS(3, 6, 2, 90, 270));
		$this->addAftSystem(new NexusMedEnhPlasma(3, 6, 4, 60, 180));

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
					10 => "Magazine",
					13 => "Scanner",
					16 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					5 => "Thruster",
					7 => "Advanced Close-In Defense System",
					10 => "Assault Cannon",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Advanced Close-In Defense System",
					10 => "Medium Enhanced Plasma",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					5 => "Thruster",
					7 => "Advanced Close-In Defense System",
					9 => "Medium Enhanced Plasma",
					11 => "Assault Cannon",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					7 => "Advanced Close-In Defense System",
					9 => "Medium Enhanced Plasma",
					11 => "Assault Cannon",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
