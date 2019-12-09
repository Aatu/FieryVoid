<?php
class MinbariOrbitalHanger extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 600;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "Minbari";
		$this->phpclass = "minbariorbitalhanger";
		$this->shipClass = "Minbari Orbital Hanger";
		$this->variantOf = "Minbari Civilian Base";
		$this->imagePath = "img/ships/MinbariCivBase.png";
		$this->canvasSize = 200;
		$this->fighters = array("heavy"=>30); 

		$this->shipSizeClass = 3; 
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->unofficial = true;
		$this->isd = 2240;

		$this->forwardDefense = 18;
		$this->sideDefense = 18;


		$this->canvasSize = 280; 

		$this->addFrontSystem(new Structure( 3, 50));
		$this->addAftSystem(new Structure( 3, 50));
		$this->addLeftSystem(new Structure( 3, 50));
		$this->addRightSystem(new Structure( 3, 50));
		$this->addPrimarySystem(new Structure( 5, 70));
		
		$this->hitChart = array(			
			0=> array(
				7 => "Structure",
				9 => "Jammer",
				13 => "Cargo Bay",
				14 => "Hangar",
				16 => "Scanner",
				18 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				2 => "Fusion Cannon",
				3 => "Cargo Bay",
				8 => "Hangar",
				9 => "Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				2 => "Fusion Cannon",
				3 => "Cargo Bay",
				8 => "Hangar",
				9 => "Reactor",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				2 => "Fusion Cannon",
				3 => "Cargo Bay",
				8 => "Hangar",
				9 => "Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				2 => "Fusion Cannon",
				3 => "Cargo Bay",
				8 => "Hangar",
				9 => "Reactor",
				18 => "Structure",
				20 => "Primary",
			),
		);


		$this->addPrimarySystem(new Reactor(5, 9, 0, 0));
		$this->addPrimarySystem(new CnC(5, 15, 0, 0)); 
		$this->addPrimarySystem(new Scanner(5, 14, 3, 6));
		$this->addPrimarySystem(new Hangar(5, 8));
		$this->addPrimarySystem(new CargoBay(5, 36));
		$this->addPrimarySystem(new Jammer(5, 8, 5));

		$this->addFrontSystem(new FusionCannon(3, 8, 1, 270, 90));
		$this->addFrontSystem(new FusionCannon(3, 8, 1, 270, 90));
		$this->addFrontSystem(new Hangar(3, 6));
		$this->addFrontSystem(new CargoBay(3, 6));
		$this->addFrontSystem(new SubReactor(3, 6, 0, 0));

		$this->addAftSystem(new FusionCannon(3, 8, 1, 90, 270));
		$this->addAftSystem(new FusionCannon(3, 8, 1, 90, 270));
		$this->addAftSystem(new Hangar(3, 6));
		$this->addAftSystem(new CargoBay(3, 6));
		$this->addAftSystem(new SubReactor(3, 6, 0, 0));
		
		$this->addRightSystem(new FusionCannon(3, 8, 1, 0, 180));
		$this->addRightSystem(new FusionCannon(3, 8, 1, 0, 180));
		$this->addRightSystem(new Hangar(3, 6));
		$this->addRightSystem(new CargoBay(3, 6));
		$this->addRightSystem(new SubReactor(3, 6, 0, 0));
		
		$this->addLeftSystem(new FusionCannon(3, 8, 1, 180, 0));
		$this->addLeftSystem(new FusionCannon(3, 8, 1, 180, 0));
		$this->addLeftSystem(new Hangar(3, 6));
		$this->addLeftSystem(new CargoBay(3, 6));
		$this->addLeftSystem(new SubReactor(3, 6, 0, 0));
		
		}
    }
?>
