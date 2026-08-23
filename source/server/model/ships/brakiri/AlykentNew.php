<?php
class AlykentNew extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 575;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "Brakiri Syndicracy";
		$this->phpclass = "AlykentNew";
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

		$this->addAftSystem(new GraviticCannon(4, 6, 5, 120, 240));
		$this->addAftSystem(new GraviticCannon(4, 6, 5, 120, 240));
		$this->addAftSystem(new GraviticBolt(3, 5, 2, 60, 240));
		$this->addAftSystem(new GraviticBolt(3, 5, 2, 120, 300));

			$shield = new GraviticShield(0, 6, 0, 2, 240, 0);			
			$shield->overkillArcStructures = array(1, 3); //overkill spills to whichever Port quarter is in arc					
			$shield->setStructureHome(array(1, 3));				
			$this->addLeftFrontSystem($shield);		

		$this->addLeftSystem(new GraviticCannon(4, 6, 5, 210, 330));
		$this->addLeftSystem(new GraviticCannon(4, 6, 5, 210, 330));
		$this->addLeftSystem(new GraviticBolt(3, 5, 2, 180, 360));
		$this->addLeftSystem(new GraviticBolt(3, 5, 2, 180, 360));

			$shield = new GraviticShield(0, 6, 0, 2, 180, 300);			
			$shield->overkillArcStructures = array(2, 3); //overkill spills to whichever Port quarter is in arc					
			$shield->setStructureHome(array(2, 3));				
			$this->addLeftAftSystem($shield);		

			$shield = new GraviticShield(0, 6, 0, 2, 0, 120);			
			$shield->overkillArcStructures = array(1, 4); //overkill spills to whichever Port quarter is in arc					
			$shield->setStructureHome(array(1, 4));				
			$this->addRightFrontSystem($shield);				

		$this->addRightSystem(new GraviticCannon(4, 6, 5, 30, 150));
		$this->addRightSystem(new GraviticCannon(4, 6, 5, 30, 150));
		$this->addRightSystem(new GraviticBolt(3, 5, 2, 0, 180));
		$this->addRightSystem(new GraviticBolt(3, 5, 2, 0, 180));

			$shield = new GraviticShield(0, 6, 0, 2, 60, 180);			
			$shield->overkillArcStructures = array(2, 4); //overkill spills to whichever Port quarter is in arc					
			$shield->setStructureHome(array(2, 4));				
			$this->addRightAftSystem($shield);	


		$this->addPrimarySystem(new Structure( 5, 52));
		$this->addFrontSystem(Structure::createAsOuter(5, 56, 300, 60));
		$this->addAftSystem(Structure::createAsOuter(5, 56, 120, 240));
		$this->addLeftSystem(Structure::createAsOuter(5, 56, 210, 330));
		$this->addRightSystem(Structure::createAsOuter(5, 56, 30, 150));
		
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
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				4 => "TAG:Gravitic Cannon",
				8 => "TAG:Gravitic Bolt",
				10 => "TAG:Gravitic Shield",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				4 => "TAG:Gravitic Cannon",
				8 => "TAG:Gravitic Bolt",
				10 => "TAG:Gravitic Shield",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				4 => "TAG:Gravitic Cannon",
				8 => "TAG:Gravitic Bolt",
				10 => "TAG:Gravitic Shield",
				18 => "Structure",
				20 => "Primary",
			),
		);

    	}
   	}
?>