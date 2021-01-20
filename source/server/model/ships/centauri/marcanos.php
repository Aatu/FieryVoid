<?php
class marcanos extends SmallStarBaseFourSections{
	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);
		$this->pointCost = 1200;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "Centauri";
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
		
		
		$this->addFrontSystem(new Structure( 4, 90));
		$this->addAftSystem(new Structure( 4, 90));
		$this->addLeftSystem(new Structure( 4, 90));
		$this->addRightSystem(new Structure( 4, 90));
		$this->addPrimarySystem(new Structure( 5, 100));
		
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
		$this->addFrontSystem(new CargoBay(4, 24));
		$this->addFrontSystem(new SubReactorUniversal(4, 20, 0, 0));

		$this->addAftSystem(new PlasmaAccelerator(4, 10, 5, 90, 270));
        	$this->addAftSystem(new TwinArray(4, 6, 2, 90, 270));
        	$this->addAftSystem(new TwinArray(4, 6, 2, 90, 270));
        	$this->addAftSystem(new TwinArray(4, 6, 2, 90, 270));
            	$this->addAftSystem(new TwinArray(4, 6, 2, 90, 270));
		$this->addAftSystem(new CargoBay(4, 24));
		$this->addAftSystem(new SubReactorUniversal(4, 20, 0, 0));
		
		$this->addRightSystem(new PlasmaAccelerator(4, 10, 5, 0, 180));
        	$this->addRightSystem(new TwinArray(4, 6, 2, 0, 180));
        	$this->addRightSystem(new TwinArray(4, 6, 2, 0, 180));
        	$this->addRightSystem(new TwinArray(4, 6, 2, 0, 180));
        	$this->addRightSystem(new TwinArray(4, 6, 2, 0, 180));
		$this->addRightSystem(new CargoBay(4, 24));
		$this->addRightSystem(new SubReactorUniversal(4, 20, 0, 0));
		
		$this->addLeftSystem(new PlasmaAccelerator(4, 10, 5, 180, 360));
        	$this->addLeftSystem(new TwinArray(4, 6, 2, 180, 360));
        	$this->addLeftSystem(new TwinArray(4, 6, 2, 180, 360));
        	$this->addLeftSystem(new TwinArray(4, 6, 2, 180, 360));
        	$this->addLeftSystem(new TwinArray(4, 6, 2, 180, 360));
		$this->addLeftSystem(new CargoBay(4, 24));
		$this->addLeftSystem(new SubReactorUniversal(4, 20, 0, 0));
		
		
    }
}
?>
