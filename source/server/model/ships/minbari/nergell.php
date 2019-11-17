<?php
class Nergell extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 1900;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "Minbari";
		$this->phpclass = "nergell";
		$this->shipClass = "Nergell Military Guardpost";
		$this->imagePath = "img/ships/MinbariGuardpost.png";
		$this->canvasSize = 200;
		$this->fighters = array("heavy"=>12); 

		$this->shipSizeClass = 3; 
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->unofficial = true;
		$this->isd = 2259;

		$this->forwardDefense = 17;
		$this->sideDefense = 17;

		$this->canvasSize = 280; 

		$this->addFrontSystem(new Structure( 6, 94));
		$this->addAftSystem(new Structure( 6, 94));
		$this->addLeftSystem(new Structure( 6, 94));
		$this->addRightSystem(new Structure( 6, 94));
		$this->addPrimarySystem(new Structure( 7, 98));
		
		$this->hitChart = array(			
			0=> array(
				10 => "Structure",
				12 => "Cargo Bay",
				13 => "Neutron Laser",
				14 => "Jammer",
				16 => "Scanner",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				2 => "Neutron Laser",
				4 => "Shock Cannon",
				9 => "Fusion Cannon",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				2 => "Neutron Laser",
				4 => "Shock Cannon",
				9 => "Fusion Cannon",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				2 => "Neutron Laser",
				4 => "Shock Cannon",
				9 => "Fusion Cannon",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				2 => "Neutron Laser",
				4 => "Shock Cannon",
				9 => "Fusion Cannon",
				18 => "Structure",
				20 => "Primary",
			),
		);

		$this->addPrimarySystem(new Reactor(7, 35, 0, 4));
		$this->addPrimarySystem(new CnC(7, 30, 0, 0)); 
		$this->addPrimarySystem(new Scanner(6, 32, 5, 12));
		$this->addPrimarySystem(new Hangar(6, 19));
		$this->addPrimarySystem(new CargoBay(7, 48));
		$this->addPrimarySystem(new Jammer(6, 8, 4));
		$this->addPrimarySystem(new NeutronLaser(7, 10, 6, 0, 360));

		$this->addFrontSystem(new NeutronLaser(4, 10, 6, 315, 45));
		$this->addFrontSystem(new ShockCannon(4, 6, 4, 270, 90));
		$this->addFrontSystem(new FusionCannon(3, 8, 1, 270, 90));
		$this->addFrontSystem(new FusionCannon(3, 8, 1, 270, 90));
		$this->addFrontSystem(new FusionCannon(3, 8, 1, 270, 90));

		$this->addAftSystem(new NeutronLaser(4, 10, 6, 135, 225));
		$this->addAftSystem(new ShockCannon(4, 6, 4, 90, 270));
		$this->addAftSystem(new FusionCannon(3, 8, 1, 90, 270));
		$this->addAftSystem(new FusionCannon(3, 8, 1, 90, 270));
		$this->addAftSystem(new FusionCannon(3, 8, 1, 90, 270));
		
		$this->addRightSystem(new NeutronLaser(4, 10, 6, 45, 135));
		$this->addRightSystem(new ShockCannon(4, 6, 4, 0, 180));
		$this->addRightSystem(new FusionCannon(3, 8, 1, 0, 180));
		$this->addRightSystem(new FusionCannon(3, 8, 1, 0, 180));
		$this->addRightSystem(new FusionCannon(3, 8, 1, 0, 180));
		
		$this->addLeftSystem(new NeutronLaser(4, 10, 6, 225, 315));
		$this->addLeftSystem(new ShockCannon(4, 6, 4, 180, 360));
		$this->addLeftSystem(new FusionCannon(3, 8, 1, 180, 360));
		$this->addLeftSystem(new FusionCannon(3, 8, 1, 180, 360));
		$this->addLeftSystem(new FusionCannon(3, 8, 1, 180, 360));
		}
    }

?>
