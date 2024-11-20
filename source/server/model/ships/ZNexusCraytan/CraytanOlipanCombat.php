<?php
class CraytanOlipanCombat extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 900;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "ZNexus Craytan Union";
		$this->phpclass = "CraytanOlipanCombat";
		$this->shipClass = "Olipan Combat Base";
		$this->imagePath = "img/ships/Nexus/craytan_olipanRefit.png";
		$this->canvasSize = 140; 
		$this->unofficial = true;
		$this->isd = 2130;

		$this->shipSizeClass = 3; 
		$this->Enormous = false;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

        $this->fighters = array("assault shuttles"=>12, "normal"=>12);

		$this->forwardDefense = 19;
		$this->sideDefense = 19;

		$this->addPrimarySystem(new Reactor(5, 30, 0, 0));
		$this->addPrimarySystem(new CnC(5, 24, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 16, 6, 6));
		$this->addPrimarySystem(new Hangar(5, 18));
		$this->addPrimarySystem(new Magazine(5, 24));
		$this->addPrimarySystem(new NexusAssaultCannonBattery(5, 16, 10, 0, 360));
		
		$this->addFrontSystem(new NexusACIDS(4, 6, 2, 270, 90));
		$this->addFrontSystem(new NexusACIDS(4, 6, 2, 270, 90));
		$this->addFrontSystem(new NexusMedEnhPlasma(4, 6, 4, 270, 90));
		$this->addFrontSystem(new NexusHeavyEnhPlasma(4, 9, 5, 270, 90));
		$this->addFrontSystem(new Hangar(4, 3));
		$this->addFrontSystem(new CargoBay(4, 25));

		$this->addAftSystem(new NexusACIDS(4, 6, 2, 90, 270));
		$this->addAftSystem(new NexusACIDS(4, 6, 2, 90, 270));
		$this->addAftSystem(new NexusMedEnhPlasma(4, 6, 4, 90, 270));
		$this->addAftSystem(new NexusHeavyEnhPlasma(4, 9, 5, 90, 270));
		$this->addAftSystem(new Hangar(4, 3));
		$this->addAftSystem(new CargoBay(4, 25));
			
		$this->addLeftSystem(new NexusACIDS(4, 6, 2, 180, 360));
		$this->addLeftSystem(new NexusACIDS(4, 6, 2, 180, 360));
		$this->addLeftSystem(new NexusMedEnhPlasma(4, 6, 4, 180, 360));
		$this->addLeftSystem(new NexusHeavyEnhPlasma(4, 9, 5, 180, 360));
		$this->addLeftSystem(new Hangar(4, 3));
		$this->addLeftSystem(new CargoBay(4, 25));

		$this->addRightSystem(new NexusACIDS(4, 6, 2, 0, 180));
		$this->addRightSystem(new NexusACIDS(4, 6, 2, 0, 180));
		$this->addRightSystem(new NexusMedEnhPlasma(4, 6, 4, 0, 180));
		$this->addRightSystem(new NexusHeavyEnhPlasma(4, 9, 5, 0, 180));
		$this->addRightSystem(new Hangar(4, 3));
		$this->addRightSystem(new CargoBay(4, 25));

		$this->addFrontSystem(new Structure( 4, 64));
		$this->addAftSystem(new Structure( 4, 64));
		$this->addLeftSystem(new Structure( 4, 64));
		$this->addRightSystem(new Structure( 4, 64));
		$this->addPrimarySystem(new Structure( 5, 80));
		
		$this->hitChart = array(			
			0=> array(
				7 => "Structure",
				9 => "Magazine",
				12 => "Assault Cannon Battery",
				14 => "Hangar",
				16 => "Scanner",
				18 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				3 => "Cargo Bay",
				5 => "Advanced Close-In Defense System",
				7 => "Medium Enhanced Plasma",
				9 => "Heavy Enhanced Plasma",
				11 => "Hangar",
				18 => "Structure",
				20 => "Primary",
				
			),
			2=> array(
				3 => "Cargo Bay",
				5 => "Advanced Close-In Defense System",
				7 => "Medium Enhanced Plasma",
				9 => "Heavy Enhanced Plasma",
				11 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				3 => "Cargo Bay",
				5 => "Advanced Close-In Defense System",
				7 => "Medium Enhanced Plasma",
				9 => "Heavy Enhanced Plasma",
				11 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				3 => "Cargo Bay",
				5 => "Advanced Close-In Defense System",
				7 => "Medium Enhanced Plasma",
				9 => "Heavy Enhanced Plasma",
				11 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
		);

    	}
   	}
?>