<?php
class CraytanOlipanRefit extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 500;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "ZNexus Craytan Union (early)";
		$this->phpclass = "CraytanOlipanRefit";
		$this->shipClass = "Olipan Supply Post (2097)";
			$this->variantOf = "Olipan Supply Post";
			$this->occurence = "common";
		$this->imagePath = "img/ships/Nexus/craytan_olipan.png";
		$this->canvasSize = 140; 
		$this->unofficial = true;
		$this->isd = 2097;

		$this->shipSizeClass = 3; 
		$this->Enormous = false;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

        $this->fighters = array("assault shuttles"=>18);

		$this->forwardDefense = 19;
		$this->sideDefense = 19;

		$this->addPrimarySystem(new Reactor(4, 25, 0, 0));
		$this->addPrimarySystem(new CnC(4, 16, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 16, 6, 6));
		$this->addPrimarySystem(new Hangar(4, 12));
		$this->addPrimarySystem(new CargoBay(4, 30));
		$this->addPrimarySystem(new Magazine(4, 24));
		
		$this->addFrontSystem(new NexusCIDS(4, 4, 2, 270, 90));
		$this->addFrontSystem(new NexusCIDS(4, 4, 2, 270, 90));
		$this->addFrontSystem(new MediumPlasma(4, 5, 3, 270, 90));
		$this->addFrontSystem(new HeavyPlasma(4, 8, 5, 270, 90));
		//$this->addFrontSystem(new Hangar(4, 3));
		//$this->addFrontSystem(new CargoBay(4, 30));

			$cargoBay = new CargoBay(4, 30);
			$cargoBay->startArc = 270;
			$cargoBay->endArc = 90;
			$this->addFrontSystem($cargoBay);
			$hangar = new Hangar(4, 3);
			$hangar->startArc = 270;
			$hangar->endArc = 90;
			$this->addFrontSystem($hangar);
					
		$this->addAftSystem(new NexusCIDS(4, 4, 2, 90, 270));
		$this->addAftSystem(new NexusCIDS(4, 4, 2, 90, 270));
		$this->addAftSystem(new MediumPlasma(4, 5, 3, 90, 270));
		$this->addAftSystem(new HeavyPlasma(4, 8, 5, 90, 270));
		//$this->addAftSystem(new Hangar(4, 3));
		//$this->addAftSystem(new CargoBay(4, 30));

			$cargoBay = new CargoBay(4, 30);
			$cargoBay->startArc = 90;
			$cargoBay->endArc = 270;
			$this->addAftSystem($cargoBay);
			$hangar = new Hangar(4, 3);
			$hangar->startArc = 90;
			$hangar->endArc = 270;
			$this->addAftSystem($hangar);
											
		$this->addLeftSystem(new NexusCIDS(4, 4, 2, 180, 360));
		$this->addLeftSystem(new NexusCIDS(4, 4, 2, 180, 360));
		$this->addLeftSystem(new MediumPlasma(4, 5, 3, 180, 360));
		$this->addLeftSystem(new HeavyPlasma(4, 8, 5, 180, 360));
		//$this->addLeftSystem(new Hangar(4, 3));
		//$this->addLeftSystem(new CargoBay(4, 30));

			$cargoBay = new CargoBay(4, 30);
			$cargoBay->startArc = 180;
			$cargoBay->endArc = 360;
			$this->addLeftSystem($cargoBay);
			$hangar = new Hangar(4, 3);
			$hangar->startArc = 180;
			$hangar->endArc = 360;
			$this->addLeftSystem($hangar);
		
		$this->addRightSystem(new NexusCIDS(4, 4, 2, 0, 180));
		$this->addRightSystem(new NexusCIDS(4, 4, 2, 0, 180));
		$this->addRightSystem(new MediumPlasma(4, 5, 3, 0, 180));
		$this->addRightSystem(new HeavyPlasma(4, 8, 5, 0, 180));
		//$this->addRightSystem(new Hangar(4, 3));
		//$this->addRightSystem(new CargoBay(4, 30));

			$cargoBay = new CargoBay(4, 30);
			$cargoBay->startArc = 0;
			$cargoBay->endArc = 180;
			$this->addRightSystem($cargoBay);
			$hangar = new Hangar(4, 3);
			$hangar->startArc = 0;
			$hangar->endArc = 180;
			$this->addRightSystem($hangar);
		
		/*replaced by TAGed versions!			
		$this->addFrontSystem(new Structure( 4, 64));
		$this->addAftSystem(new Structure( 4, 64));
		$this->addLeftSystem(new Structure( 4, 64));
		$this->addRightSystem(new Structure( 4, 64));
		$this->addPrimarySystem(new Structure( 4, 80));
		*/
		$this->addPrimarySystem(new Structure( 4, 80));//needs to be called first for some reason - static call apparently fails for the first time...
		$this->addFrontSystem(Structure::createAsOuter(4, 64, 270,90));
		$this->addAftSystem(Structure::createAsOuter(4, 64, 90, 270));
		$this->addLeftSystem(Structure::createAsOuter(4, 64, 180, 360));
		$this->addRightSystem(Structure::createAsOuter(4, 64, 0, 180));	

		$this->hitChart = array(			
			0=> array(
				7 => "Structure",
				9 => "Magazine",
				12 => "Cargo Bay",
				14 => "Hangar",
				16 => "Scanner",
				18 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				3 => "TAG:Cargo Bay",
				5 => "TAG:Close-In Defense System",
				7 => "TAG:Medium Plasma Cannon",
				9 => "TAG:Heavy Plasma Cannon",
				11 => "TAG:Hangar",
				18 => "Structure",
				20 => "Primary",
				
			),
			2=> array(
				3 => "TAG:Cargo Bay",
				5 => "TAG:Close-In Defense System",
				7 => "TAG:Medium Plasma Cannon",
				9 => "TAG:Heavy Plasma Cannon",
				11 => "TAG:Hangar",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				3 => "TAG:Cargo Bay",
				5 => "TAG:Close-In Defense System",
				7 => "TAG:Medium Plasma Cannon",
				9 => "TAG:Heavy Plasma Cannon",
				11 => "TAG:Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				3 => "TAG:Cargo Bay",
				5 => "TAG:Close-In Defense System",
				7 => "TAG:Medium Plasma Cannon",
				9 => "TAG:Heavy Plasma Cannon",
				11 => "TAG:Hangar",
				18 => "Structure",
				20 => "Primary",
			),
		);

    	}
   	}
?>