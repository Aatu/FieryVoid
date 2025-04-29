<?php
class marcanos extends SmallStarBaseFourSections{
	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);
		$this->pointCost = 1200;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "Centauri Republic";
		$this->phpclass = "marcanos";
		$this->shipClass = "Marcanos Civilian Base";
		$this->imagePath = "img/ships/marcanos.png";
		$this->fighters = array("light"=>24); 
		$this->shipSizeClass = 3; 
		$this->Enormous = true;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->forwardDefense = 18;
		$this->sideDefense = 18;
		$this->canvasSize = 200; 
		$this->isd = 2113;
		
		/*replace outer Structures with tagged ones
		$this->addFrontSystem(new Structure( 4, 90));
		$this->addAftSystem(new Structure( 4, 90));
		$this->addLeftSystem(new Structure( 4, 90));
		$this->addRightSystem(new Structure( 4, 90));
		*/		
		$this->addPrimarySystem(new Structure( 5, 100)); //needs to be called first for some reason - static call apparently fails fot the first time...
		$this->addFrontSystem(Structure::createAsOuter(4, 90,270,90));
		$this->addAftSystem(Structure::createAsOuter(4, 90, 90, 270));
		$this->addLeftSystem(Structure::createAsOuter(4, 90, 180, 360));
		$this->addRightSystem(Structure::createAsOuter(4, 90, 0, 180));
		
		
		/*replaced by simpler version above
		$structArmor = 4;
		$structHP = 90;
		
		$struct = new Structure( $structArmor, $structHP);
		$struct->addTag("Outer Structure");
		$struct->startArc = 270;
		$struct->endArc = 90;
        $this->addFrontSystem($struct);
		
		$struct = new Structure( $structArmor, $structHP);
		$struct->addTag("Outer Structure");
		$struct->startArc = 90;
		$struct->endArc = 270;
        $this->addAftSystem($struct);
		
		$struct = new Structure( $structArmor, $structHP);
		$struct->addTag("Outer Structure");
		$struct->startArc = 180;
		$struct->endArc = 0;
        $this->addLeftSystem($struct);
		
		$struct = new Structure( $structArmor, $structHP);
		$struct->addTag("Outer Structure");
		$struct->startArc = 0;
		$struct->endArc = 180;
        $this->addRightSystem($struct);
		*/
		
		
		
		$this->hitChart = array(			
			0=> array(
				10 => "Structure",
				12 => "Twin Array",
				14 => "Scanner",
				16 => "Hangar",
				18 => "Reactor",
				20 => "C&C", 
			),
			
			1=> array(
				4 => "TAG:Twin Array", //this will catch PRIMARY TAs as well, but I don't think it's a problem
				6 => "TAG:Plasma Accelerator",
				8 => "TAG:Cargo Bay",
				9 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				4 => "TAG:Twin Array",
				6 => "TAG:Plasma Accelerator",
				8 => "TAG:Cargo Bay",
				9 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				4 => "TAG:Twin Array",
				6 => "TAG:Plasma Accelerator",
				8 => "TAG:Cargo Bay",
				9 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				4 => "TAG:Twin Array",
				6 => "TAG:Plasma Accelerator",
				8 => "TAG:Cargo Bay",
				9 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			
			/* replaced with TAG system!
			1=> array(
				4 => "Twin Array",
				6 => "Plasma Accelerator",
				8 => "Cargo Bay",
				9 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				4 => "Twin Array",
				6 => "Plasma Accelerator",
				8 => "Cargo Bay",
				9 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				4 => "Twin Array",
				6 => "Plasma Accelerator",
				8 => "Cargo Bay",
				9 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				4 => "Twin Array",
				6 => "Plasma Accelerator",
				8 => "Cargo Bay",
				9 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			*/
		);
		$this->addPrimarySystem(new Reactor(5, 28, 0, 0));
		$this->addPrimarySystem(new CnC(5, 25, 0, 0)); 
		$this->addPrimarySystem(new Scanner(5, 20, 3, 6));
		$this->addPrimarySystem(new Scanner(5, 20, 3, 6));
		$this->addPrimarySystem(new Hangar(5, 14));
		$this->addPrimarySystem(new Hangar(5, 14));
        	$this->addPrimarySystem(new TwinArray(5, 6, 2, 0, 360));
        	$this->addPrimarySystem(new TwinArray(5, 6, 2, 0, 360));
        	$this->addPrimarySystem(new TwinArray(5, 6, 2, 0, 360));
        	$this->addPrimarySystem(new TwinArray(5, 6, 2, 0, 360));

		$this->addFrontSystem(new PlasmaAccelerator(4, 10, 5, 270, 90));
        	$this->addFrontSystem(new TwinArray(4, 6, 2, 270, 90));
        	$this->addFrontSystem(new TwinArray(4, 6, 2, 270, 90));
        	$this->addFrontSystem(new TwinArray(4, 6, 2, 270, 90));
        	$this->addFrontSystem(new TwinArray(4, 6, 2, 270, 90));
			/*Cargo Bays and SubReactors need to have arc defined for TAG to work*/
			/*
			$this->addFrontSystem(new CargoBay(4, 24));
			$this->addFrontSystem(new SubReactorUniversal(4, 20, 0, 0));
			*/
			$cargoBay = new CargoBay(4, 24);
			$cargoBay->startArc = 270;
			$cargoBay->endArc = 90;
			$this->addFrontSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 20, 0, 0);
			$subReactor->startArc = 270;
			$subReactor->endArc = 90;
			$this->addFrontSystem($subReactor);

		$this->addAftSystem(new PlasmaAccelerator(4, 10, 5, 90, 270));
        	$this->addAftSystem(new TwinArray(4, 6, 2, 90, 270));
        	$this->addAftSystem(new TwinArray(4, 6, 2, 90, 270));
        	$this->addAftSystem(new TwinArray(4, 6, 2, 90, 270));
            	$this->addAftSystem(new TwinArray(4, 6, 2, 90, 270));
			/*Cargo Bays and SubReactors need to have arc defined for TAG to work*/
			/*
		$this->addAftSystem(new CargoBay(4, 24));
		$this->addAftSystem(new SubReactorUniversal(4, 20, 0, 0));
			*/
			$cargoBay = new CargoBay(4, 24);
			$cargoBay->startArc = 90;
			$cargoBay->endArc = 270;
			$this->addAftSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 20, 0, 0);
			$subReactor->startArc = 90;
			$subReactor->endArc = 270;
			$this->addAftSystem($subReactor);
		
		$this->addRightSystem(new PlasmaAccelerator(4, 10, 5, 0, 180));
        	$this->addRightSystem(new TwinArray(4, 6, 2, 0, 180));
        	$this->addRightSystem(new TwinArray(4, 6, 2, 0, 180));
        	$this->addRightSystem(new TwinArray(4, 6, 2, 0, 180));
        	$this->addRightSystem(new TwinArray(4, 6, 2, 0, 180));
			/*Cargo Bays and SubReactors need to have arc defined for TAG to work*/
			/*
		$this->addRightSystem(new CargoBay(4, 24));
		$this->addRightSystem(new SubReactorUniversal(4, 20, 0, 0));
			*/
			$cargoBay = new CargoBay(4, 24);
			$cargoBay->startArc = 0;
			$cargoBay->endArc = 180;
			$this->addRightSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 20, 0, 0);
			$subReactor->startArc = 0;
			$subReactor->endArc = 180;
			$this->addRightSystem($subReactor);
		
		$this->addLeftSystem(new PlasmaAccelerator(4, 10, 5, 180, 360));
        	$this->addLeftSystem(new TwinArray(4, 6, 2, 180, 360));
        	$this->addLeftSystem(new TwinArray(4, 6, 2, 180, 360));
        	$this->addLeftSystem(new TwinArray(4, 6, 2, 180, 360));
        	$this->addLeftSystem(new TwinArray(4, 6, 2, 180, 360));
			/*Cargo Bays and SubReactors need to have arc defined for TAG to work*/
			/*
		$this->addLeftSystem(new CargoBay(4, 24));
		$this->addLeftSystem(new SubReactorUniversal(4, 20, 0, 0));
			*/
			$cargoBay = new CargoBay(4, 24);
			$cargoBay->startArc = 180;
			$cargoBay->endArc = 360;
			$this->addLeftSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 20, 0, 0);
			$subReactor->startArc = 180;
			$subReactor->endArc = 360;
			$this->addLeftSystem($subReactor);
		
		
    }
}
?>
