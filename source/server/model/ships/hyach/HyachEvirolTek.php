<?php
class HyachEvirolTek extends BaseShip{
	public $HyachSpecialists;    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 380;
	$this->faction = "Hyach Gerontocracy";
        $this->phpclass = "HyachEvirolTek";
        $this->imagePath = "img/ships/HyachEvirolTek.png";
        $this->shipClass = "Evirol Tek Logistics Cruiser";
        $this->shipSizeClass = 3;

        $this->isd = 2216;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 4;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->gravitic = true;

        $this->addPrimarySystem(new Reactor(4, 23, 0, 0));
        $this->addPrimarySystem(new CnC(5, 15, 0, 0));
		$sensors = new Scanner(4, 20, 6, 8);
			$sensors->markHyach();
			$this->addPrimarySystem($sensors); 

        $this->addPrimarySystem(new Engine(5, 20, 0, 10, 4));
		$this->addPrimarySystem(new Hangar(4, 4));
		$this->addPrimarySystem(new CargoBay(4, 25));
		$this->addPrimarySystem(new JumpEngine(4, 15, 4, 20));

        $this->addFrontSystem(new GraviticThruster(4, 10, 0, 2, 1));
        $this->addFrontSystem(new GraviticThruster(4, 10, 0, 2, 1));
		$this->addFrontSystem(new MediumLaser(4, 6, 5, 300, 60));
		$this->addFrontSystem(new Interdictor(2, 4, 1, 270, 90));

		$this->addAftSystem(new Interdictor(2, 4, 1, 90, 270));
        $this->addAftSystem(new GraviticThruster(5, 16, 0, 5, 2));
        $this->addAftSystem(new GraviticThruster(5, 16, 0, 5, 2));

        $this->addLeftSystem(new GraviticThruster(4, 15, 0, 5, 3));
        $this->addLeftSystem(new Maser(2, 6, 3, 180, 300));
        $this->addLeftSystem(new Maser(2, 6, 3, 240, 360));
		$this->addLeftSystem(new CargoBay(4, 24));

        $this->addRightSystem(new GraviticThruster(4, 15, 0, 5, 4));
        $this->addRightSystem(new Maser(2, 6, 3, 60, 180));
        $this->addRightSystem(new Maser(2, 6, 3, 0, 120));
		$this->addRightSystem(new CargoBay(4, 24));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 42));
        $this->addAftSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 5, 48));
        $this->addLeftSystem(new Structure( 5, 48));
        $this->addRightSystem(new Structure( 5, 48 ));
        
        $this->hitChart = array(
        		0=> array(
        				8 => "Structure",
						10 => "Cargo Bay",
        				12 => "Jump Engine",
        				14 => "Scanner",
        				16 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				5 => "Thruster",
        				7 => "Medium Laser",
						9 => "Interdictor",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				8 => "Interdictor",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				4 => "Thruster",
        				6 => "Maser",
        				10 => "Cargo Bay",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				4 => "Thruster",
        				6 => "Maser",
        				10 => "Cargo Bay",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}

?>