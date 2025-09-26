<?php
class Alykent extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 575;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "Brakiri Syndicracy";
		$this->phpclass = "Alykent";
		$this->shipClass = "Alykent Guardpost";
		$this->imagePath = "img/ships/Alykent.png";
		$this->canvasSize = 200; 
		$this->isd = 2198;

		$this->shipSizeClass = 3; 
		$this->Enormous = false;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 17;
		$this->sideDefense = 17;


		$this->addPrimarySystem(new Reactor(5, 30, 0, 4));
		$this->addPrimarySystem(new CnC(5, 16, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 16, 6, 8));
		$this->addPrimarySystem(new Hangar(5, 4, 1));
		$this->addPrimarySystem(new ShieldGenerator(5, 12, 4, 4));		
		
		$this->addFrontSystem(new GraviticCannon(4, 6, 5, 300, 60));
		$this->addFrontSystem(new GraviticCannon(4, 6, 5, 300, 60));
		$this->addFrontSystem(new GraviticBolt(3, 5, 2, 240, 60));
		$this->addFrontSystem(new GraviticBolt(3, 5, 2, 300, 120));
		$this->addFrontSystem(new GraviticShield(0, 6, 0, 2, 300, 60));

		$this->addAftSystem(new GraviticCannon(4, 6, 5, 120, 240));
		$this->addAftSystem(new GraviticCannon(4, 6, 5, 120, 240));
		$this->addAftSystem(new GraviticBolt(3, 5, 2, 60, 240));
		$this->addAftSystem(new GraviticBolt(3, 5, 2, 120, 300));
		$this->addAftSystem(new GraviticShield(0, 6, 0, 2, 120, 240));
			
		$this->addLeftSystem(new GraviticCannon(4, 6, 5, 210, 330));
		$this->addLeftSystem(new GraviticCannon(4, 6, 5, 210, 330));
		$this->addLeftSystem(new GraviticBolt(3, 5, 2, 180, 360));
		$this->addLeftSystem(new GraviticBolt(3, 5, 2, 180, 360));
		$this->addLeftSystem(new GraviticShield(0, 6, 0, 2, 210, 330));

		$this->addRightSystem(new GraviticCannon(4, 6, 5, 30, 150));
		$this->addRightSystem(new GraviticCannon(4, 6, 5, 30, 150));
		$this->addRightSystem(new GraviticBolt(3, 5, 2, 0, 180));
		$this->addRightSystem(new GraviticBolt(3, 5, 2, 0, 180));
		$this->addRightSystem(new GraviticShield(0, 6, 0, 2, 30, 150));	


		/*replaced by TAGed versions!
		$this->addFrontSystem(new Structure( 5, 56));
		$this->addAftSystem(new Structure( 5, 56));
		$this->addLeftSystem(new Structure( 5, 56));
		$this->addRightSystem(new Structure( 5, 56));
		*/
		$this->addPrimarySystem(new Structure( 5, 52));
		$this->addFrontSystem(Structure::createAsOuter(5, 56, 270,90));
		$this->addAftSystem(Structure::createAsOuter(5, 56, 90, 270));
		$this->addLeftSystem(Structure::createAsOuter(5, 56, 180, 360));
		$this->addRightSystem(Structure::createAsOuter(5, 56, 0, 180));
		
		$this->hitChart = array(			
			0=> array(
				11 => "Structure",
				13 => "Shield Generator",
				15 => "Scanner",
				16 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				4 => "TAG:Gravitic Cannon",
				8 => "TAG:Gravitic Bolt",
				10 => "TAG:Gravitic Shield",
				18 => "Outer Structure",
				20 => "Primary",
			),
			2=> array(
				4 => "TAG:Gravitic Cannon",
				8 => "TAG:Gravitic Bolt",
				10 => "TAG:Gravitic Shield",
				18 => "Outer Structure",
				20 => "Primary",
			),
			3=> array(
				4 => "TAG:Gravitic Cannon",
				8 => "TAG:Gravitic Bolt",
				10 => "TAG:Gravitic Shield",
				18 => "Outer Structure",
				20 => "Primary",
			),
			4=> array(
				4 => "TAG:Gravitic Cannon",
				8 => "TAG:Gravitic Bolt",
				10 => "TAG:Gravitic Shield",
				18 => "Outer Structure",
				20 => "Primary",
			),
		);

    	}
   	}
?>