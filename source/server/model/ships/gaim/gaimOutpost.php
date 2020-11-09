<?php
class gaimOutpost extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 600;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "Gaim";
		$this->phpclass = "gaimOutpost";
		$this->shipClass = "Outpost";
		$this->imagePath = "img/ships/GaimOutpost.png";
		$this->fighters = array("medium"=>12); 


		$this->shipSizeClass = 3; 
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 16;
		$this->sideDefense = 16;

		$this->canvasSize = 200; 

		$this->isd = 2260;

		$this->addPrimarySystem(new Reactor(5, 30, 0, 0));
		$this->addPrimarySystem(new CnC(5, 16, 0, 0)); 
		$this->addPrimarySystem(new Scanner(5, 20, 5, 8));
		$this->addPrimarySystem(new Hangar(5, 14));

		$this->addFrontSystem(new ScatterGun(4, 8, 3, 300, 60));
		$this->addFrontSystem(new ScatterGun(4, 8, 3, 300, 60));
		$this->addFrontSystem(new ParticleConcentrator(4, 9, 7, 300, 60));
		$this->addFrontSystem(new ParticleConcentrator(4, 9, 7, 300, 60));
		$this->addFrontSystem(new PacketTorpedo(4, 6, 5, 300, 60));
		$this->addFrontSystem(new Bulkhead(0, 4));
		$this->addFrontSystem(new Bulkhead(0, 4));

		$this->addAftSystem(new ScatterGun(4, 8, 3, 120, 240));
		$this->addAftSystem(new ScatterGun(4, 8, 3, 120, 240));
		$this->addAftSystem(new ParticleConcentrator(4, 9, 7, 120, 240));
		$this->addAftSystem(new ParticleConcentrator(4, 9, 7, 120, 240));
		$this->addAftSystem(new PacketTorpedo(4, 6, 5, 120, 240));
		$this->addAftSystem(new Bulkhead(0, 4));
		$this->addAftSystem(new Bulkhead(0, 4));
		
		$this->addLeftSystem(new ScatterGun(4, 8, 3, 210, 330));
		$this->addLeftSystem(new ScatterGun(4, 8, 3, 210, 330));
		$this->addLeftSystem(new ParticleConcentrator(4, 9, 7, 210, 330));
		$this->addLeftSystem(new ParticleConcentrator(4, 9, 7, 210, 330));
		$this->addLeftSystem(new PacketTorpedo(4, 6, 5, 210, 330));
		$this->addLeftSystem(new Bulkhead(0, 4));
		$this->addLeftSystem(new Bulkhead(0, 4));
		
		$this->addRightSystem(new ScatterGun(4, 8, 3, 30, 150));
		$this->addRightSystem(new ScatterGun(4, 8, 3, 30, 150));
		$this->addRightSystem(new ParticleConcentrator(4, 9, 7, 30, 150));
		$this->addRightSystem(new ParticleConcentrator(4, 9, 7, 30, 150));
		$this->addRightSystem(new PacketTorpedo(4, 6, 5, 30, 150));
		$this->addRightSystem(new Bulkhead(0, 4));
		$this->addRightSystem(new Bulkhead(0, 4));

		$this->addFrontSystem(new Structure( 4, 60));
		$this->addAftSystem(new Structure( 4, 60));
		$this->addLeftSystem(new Structure( 4, 60));
		$this->addRightSystem(new Structure( 4, 60));
		$this->addPrimarySystem(new Structure( 5, 60));
		
		$this->hitChart = array(			
			0=> array(
				12 => "Structure",
				14 => "Scanner",
				16 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				4 => "Packet Torpedo",
				8 => "Scattergun",
				10 => "Particle Concentrator",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				4 => "Packet Torpedo",
				8 => "Scattergun",
				10 => "Particle Concentrator",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				4 => "Packet Torpedo",
				8 => "Scattergun",
				10 => "Particle Concentrator",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				4 => "Packet Torpedo",
				8 => "Scattergun",
				10 => "Particle Concentrator",
				18 => "Structure",
				20 => "Primary",
			),
		);



		
		}
    }
?>
