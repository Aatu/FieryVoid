<?php
class ChoukaPenanceBase extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 550;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "ZEscalation Chouka Theocracy";
		$this->phpclass = "ChoukaPenanceBase";
		$this->shipClass = "Penance Military Base";
		$this->imagePath = "img/ships/EscalationWars/ChoukaTemple.png";
		$this->fighters = array("normal"=>12); 

		$this->shipSizeClass = 3; 
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 15;
		$this->sideDefense = 15;

		$this->canvasSize = 170; 
		$this->unofficial = true;

		$this->isd = 1966;

		$this->addPrimarySystem(new Reactor(4, 17, 0, 0));
		$this->addPrimarySystem(new CnC(4, 16, 0, 0)); 
		$this->addPrimarySystem(new Scanner(4, 12, 6, 6));
		$this->addPrimarySystem(new Hangar(4, 16));

		$this->addFrontSystem(new EWHeavyPointPlasmaGun(3, 7, 2, 270, 90));
		$this->addFrontSystem(new SMissileRack(3, 6, 0, 270, 90, true));
		$this->addFrontSystem(new SMissileRack(3, 6, 0, 270, 90, true));
		$this->addFrontSystem(new MediumPlasma(3, 5, 3, 270, 90));
		$this->addFrontSystem(new MediumPlasma(3, 5, 3, 270, 90));
		$this->addFrontSystem(new CargoBay(3, 15));
		$this->addFrontSystem(new CargoBay(3, 15));

		$this->addAftSystem(new EWHeavyPointPlasmaGun(3, 7, 2, 90, 270));
		$this->addAftSystem(new SMissileRack(3, 6, 0, 90, 270, true));
		$this->addAftSystem(new SMissileRack(3, 6, 0, 90, 270, true));
		$this->addAftSystem(new MediumPlasma(3, 5, 3, 90, 270));
		$this->addAftSystem(new MediumPlasma(3, 5, 3, 90, 270));
		$this->addAftSystem(new CargoBay(3, 15));
		$this->addAftSystem(new CargoBay(3, 15));

		$this->addLeftSystem(new EWHeavyPointPlasmaGun(3, 7, 2, 180, 360));
		$this->addLeftSystem(new SMissileRack(3, 6, 0, 180, 360, true));
		$this->addLeftSystem(new SMissileRack(3, 6, 0, 180, 360, true));
		$this->addLeftSystem(new MediumPlasma(3, 5, 3, 180, 360));
		$this->addLeftSystem(new MediumPlasma(3, 5, 3, 180, 360));
		$this->addLeftSystem(new CargoBay(3, 15));
		$this->addLeftSystem(new CargoBay(3, 15));

		$this->addRightSystem(new EWHeavyPointPlasmaGun(3, 7, 2, 0, 180));
		$this->addRightSystem(new SMissileRack(3, 6, 0, 0, 180, true));
		$this->addRightSystem(new SMissileRack(3, 6, 0, 0, 180, true));
		$this->addRightSystem(new MediumPlasma(3, 5, 3, 0, 180));
		$this->addRightSystem(new MediumPlasma(3, 5, 3, 0, 180));
		$this->addRightSystem(new CargoBay(3, 15));
		$this->addRightSystem(new CargoBay(3, 15));

		$this->addFrontSystem(new Structure( 3, 50));
		$this->addAftSystem(new Structure( 3, 50));
		$this->addLeftSystem(new Structure( 3, 50));
		$this->addRightSystem(new Structure( 3, 50));
		$this->addPrimarySystem(new Structure( 4, 64));
		
		$this->hitChart = array(			
			0=> array(
				10 => "Structure",
				13 => "Scanner",
				16 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				3 => "Medium Plasma Cannon",
				5 => "Heavy Point Plasma Gun",
				9 => "Cargo Bay",
				11 => "Class-S Missile Rack",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				3 => "Medium Plasma Cannon",
				5 => "Heavy Point Plasma Gun",
				9 => "Cargo Bay",
				11 => "Class-S Missile Rack",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				3 => "Medium Plasma Cannon",
				5 => "Heavy Point Plasma Gun",
				9 => "Cargo Bay",
				11 => "Class-S Missile Rack",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				3 => "Medium Plasma Cannon",
				5 => "Heavy Point Plasma Gun",
				9 => "Cargo Bay",
				11 => "Class-S Missile Rack",
				18 => "Structure",
				20 => "Primary",
			),
		);



		
		}
    }
?>
