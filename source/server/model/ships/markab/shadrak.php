<?php
class Shadrak extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 600;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "Markab";
		$this->phpclass = "Shadrak";
		$this->shipClass = "Shadrak Shrine";
		$this->imagePath = "img/ships/MarkabShadrakShrine.png";
		$this->canvasSize = 200; 
		$this->fighters = array("normal"=>6); 

		$this->shipSizeClass = 3; 
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 16;
		$this->sideDefense = 16;


		$this->addFrontSystem(new Structure( 4, 60));
		$this->addAftSystem(new Structure( 4, 60));
		$this->addLeftSystem(new Structure( 4, 60));
		$this->addRightSystem(new Structure( 4, 60));
		$this->addPrimarySystem(new Structure( 5, 60));
		
		$this->hitChart = array(			
			0=> array(
				11 => "Structure",
				13 => "Cargo Bay",
				15 => "Scanner",
				16 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				4 => "Heavy Plasma Cannon",
				8 => "Scattergun",
				10 => "Stun Beam",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				4 => "Heavy Plasma Cannon",
				8 => "Scattergun",
				10 => "Stun Beam",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				4 => "Heavy Plasma Cannon",
				8 => "Scattergun",
				10 => "Stun Beam",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				4 => "Heavy Plasma Cannon",
				8 => "Scattergun",
				10 => "Stun Beam",
				18 => "Structure",
				20 => "Primary",
			),
		);
		
		$this->addPrimarySystem(new Reactor(5, 30, 0, 0));
		$this->addPrimarySystem(new CnC(5, 16, 0, 0)); 
		$this->addPrimarySystem(new ElintScanner(5, 20, 5, 8));
		$this->addPrimarySystem(new Hangar(5, 8));
		$this->addPrimarySystem(new CargoBay(5, 48));
		
		$this->addFrontSystem(new StunBeam(4, 0, 0, 300, 60));
		$this->addFrontSystem(new HeavyPlasma(4, 8, 5, 300, 60));
		$this->addFrontSystem(new HeavyPlasma(4, 8, 5, 300, 60));
		$this->addFrontSystem(new ScatterGun(4, 0, 0, 300, 60));
		$this->addFrontSystem(new ScatterGun(4, 0, 0, 300, 60));
		
		$this->addAftSystem(new StunBeam(4, 0, 0, 120, 240));
		$this->addAftSystem(new HeavyPlasma(4, 8, 5, 120, 240));
		$this->addAftSystem(new HeavyPlasma(4, 8, 5, 120, 240));
		$this->addAftSystem(new ScatterGun(4, 0, 0, 120, 240));
		$this->addAftSystem(new ScatterGun(4, 0, 0, 120, 240));
		
		$this->addRightSystem(new StunBeam(4, 0, 0, 30, 150));
		$this->addRightSystem(new HeavyPlasma(4, 8, 5, 30, 150));
		$this->addRightSystem(new HeavyPlasma(4, 8, 5, 30, 150));
		$this->addRightSystem(new ScatterGun(4, 0, 0, 30, 150));
		$this->addRightSystem(new ScatterGun(4, 0, 0, 30, 150));
		
		$this->addLeftSystem(new StunBeam(4, 0, 0, 150, 270));
		$this->addLeftSystem(new HeavyPlasma(4, 8, 5, 150, 270));
		$this->addLeftSystem(new HeavyPlasma(4, 8, 5, 150, 270));
		$this->addLeftSystem(new ScatterGun(4, 0, 0, 150, 270));
		$this->addLeftSystem(new ScatterGun(4, 0, 0, 150, 270));		
		}
    }
