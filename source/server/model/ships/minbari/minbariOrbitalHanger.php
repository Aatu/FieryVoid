<?php
class MinbariOrbitalHanger extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 600;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "Minbari Federation";
		$this->phpclass = "minbariorbitalhanger";
		$this->shipClass = "Minbari Orbital Hangar";
		$this->variantOf = "Minbari Civilian Base";
		$this->imagePath = "img/ships/MinbariCivBase.png";
		$this->canvasSize = 200;
		$this->fighters = array("heavy"=>30, "shuttles"=>4); 

		$this->shipSizeClass = 3; 
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->unofficial = true;
		$this->isd = 2240;

		$this->forwardDefense = 18;
		$this->sideDefense = 18;

		$this->canvasSize = 280; 

		/*replaced by TAGed versions!
		$this->addFrontSystem(new Structure( 3, 50));
		$this->addAftSystem(new Structure( 3, 50));
		$this->addLeftSystem(new Structure( 3, 50));
		$this->addRightSystem(new Structure( 3, 50));
		$this->addPrimarySystem(new Structure( 5, 70));
		*/
		$this->addPrimarySystem(new Structure( 5, 70));//needs to be called first for some reason - static call apparently fails for the first time...
		$this->addFrontSystem(Structure::createAsOuter(3, 50, 270,90));
		$this->addAftSystem(Structure::createAsOuter(3, 50, 90, 270));
		$this->addLeftSystem(Structure::createAsOuter(3, 50, 180, 360));
		$this->addRightSystem(Structure::createAsOuter(3, 50, 0, 180));
		
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
				2 => "TAG:Fusion Cannon",
				3 => "TAG:Cargo Bay",
				8 => "TAG:Hangar",
				9 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				2 => "TAG:Fusion Cannon",
				3 => "TAG:Cargo Bay",
				8 => "TAG:Hangar",
				9 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				2 => "TAG:Fusion Cannon",
				3 => "TAG:Cargo Bay",
				8 => "TAG:Hangar",
				9 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				2 => "TAG:Fusion Cannon",
				3 => "TAG:Cargo Bay",
				8 => "TAG:Hangar",
				9 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
		);


		$this->addPrimarySystem(new Reactor(5, 9, 0, 0));
		$this->addPrimarySystem(new CnC(5, 15, 0, 0)); 
		$this->addPrimarySystem(new Scanner(5, 14, 3, 6));
		$this->addPrimarySystem(new Hangar(5, 10));
		$this->addPrimarySystem(new CargoBay(5, 36));
		$this->addPrimarySystem(new Jammer(5, 8, 5));

		$this->addFrontSystem(new FusionCannon(3, 8, 1, 270, 90));
		$this->addFrontSystem(new FusionCannon(3, 8, 1, 270, 90));
		//$this->addFrontSystem(new Hangar(3, 6));
		//$this->addFrontSystem(new CargoBay(3, 6));
		//$this->addFrontSystem(new SubReactorUniversal(3, 6, 0, 0));

			$hangar = new Hangar(3, 6);
			$hangar->startArc = 270;
			$hangar->endArc = 90;
			$this->addFrontSystem($hangar);
			$cargoBay = new CargoBay(3, 6);
			$cargoBay->startArc = 270;
			$cargoBay->endArc = 90;
			$this->addFrontSystem($cargoBay);
			$subReactor = new SubReactorUniversal(3, 6, 0, 0);
			$subReactor->startArc = 270;
			$subReactor->endArc = 90;
			$this->addFrontSystem($subReactor);	

		$this->addAftSystem(new FusionCannon(3, 8, 1, 90, 270));
		$this->addAftSystem(new FusionCannon(3, 8, 1, 90, 270));
		//$this->addAftSystem(new Hangar(3, 6));
		//$this->addAftSystem(new CargoBay(3, 6));
		//$this->addAftSystem(new SubReactorUniversal(3, 6, 0, 0));

			$hangar = new Hangar(3, 6);
			$hangar->startArc = 90;
			$hangar->endArc = 270;
			$this->addAftSystem($hangar);
			$cargoBay = new CargoBay(3, 6);
			$cargoBay->startArc = 90;
			$cargoBay->endArc = 270;
			$this->addAftSystem($cargoBay);
			$subReactor = new SubReactorUniversal(3, 6, 0, 0);
			$subReactor->startArc = 90;
			$subReactor->endArc = 270;
			$this->addAftSystem($subReactor);

		$this->addRightSystem(new FusionCannon(3, 8, 1, 0, 180));
		$this->addRightSystem(new FusionCannon(3, 8, 1, 0, 180));
		//$this->addRightSystem(new Hangar(3, 6));
		//$this->addRightSystem(new CargoBay(3, 6));
		//$this->addRightSystem(new SubReactorUniversal(3, 6, 0, 0));

			$hangar = new Hangar(3, 6);
			$hangar->startArc = 0;
			$hangar->endArc = 180;
			$this->addRightSystem($hangar);
			$cargoBay = new CargoBay(3, maxhealth: 6);
			$cargoBay->startArc = 0;
			$cargoBay->endArc = 180;
			$this->addRightSystem($cargoBay);
			$subReactor = new SubReactorUniversal(3, 6, 0, 0);
			$subReactor->startArc = 0;
			$subReactor->endArc = 180;
			$this->addRightSystem($subReactor);
		
		$this->addLeftSystem(new FusionCannon(3, 8, 1, 180, 0));
		$this->addLeftSystem(new FusionCannon(3, 8, 1, 180, 0));
		//$this->addLeftSystem(new Hangar(3, 6));
		//$this->addLeftSystem(new CargoBay(3, 6));
		//$this->addLeftSystem(new SubReactorUniversal(3, 6, 0, 0));

			$hangar = new Hangar(3, 6);
			$hangar->startArc = 180;
			$hangar->endArc = 360;
			$this->addLeftSystem($hangar);
			$cargoBay = new CargoBay(3, 6);
			$cargoBay->startArc = 180;
			$cargoBay->endArc = 360;
			$this->addLeftSystem($cargoBay);
			$subReactor = new SubReactorUniversal(3, 6, 0, 0);
			$subReactor->startArc = 180;
			$subReactor->endArc = 360;
			$this->addLeftSystem($subReactor);
		
		}
    }
?>
