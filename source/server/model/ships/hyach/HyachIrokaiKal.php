<?php
class HyachIrokaiKal extends BaseShip{
	public $HyachSpecialists;    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 1325;
	$this->faction = "Hyach Gerontocracy";
        $this->phpclass = "HyachIrokaiKal";
        $this->imagePath = "img/ships/HyachIrokaiKam.png";
		$this->canvasSize = 280;    
        $this->shipClass = "Irokai Kal Command Gunship";
			$this->variantOf = 'Irokai Kam Battlecruiser';
			$this->occurence = "rare";
        $this->shipSizeClass = 3;

		$this->notes = "Provides +5 initiative to all friendly Hyach units.";

        $this->isd = 2165;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 5;
        
        $this->gravitic = true;
       

        $this->addPrimarySystem(new Reactor(5, 28, 0, 0));
        $this->addPrimarySystem(new CnC(5, 26, 0, 1));
		$sensors = new Scanner(5, 30, 8, 12);
			$sensors->markHyach();
			$this->addPrimarySystem($sensors); 

        $this->addPrimarySystem(new Engine(5, 26, 0, 12, 3));
		$this->addPrimarySystem(new Hangar(4, 2));
		$this->addPrimarySystem(new JumpEngine(5, 21, 4, 20));
		$this->addPrimarySystem(new HyachComputer(5, 15, 0, 3));//$armour, $maxhealth, $powerReq, $output		
		$HyachSpecialists = $this->createHyachSpecialists(2); //$specTotal
			$this->addPrimarySystem( $HyachSpecialists );	

        $this->addFrontSystem(new GraviticThruster(4, 11, 0, 3, 1));
        $this->addFrontSystem(new GraviticThruster(4, 11, 0, 3, 1));
		$this->addFrontSystem(new SpinalLaser(5, 12, 12, 330, 30));
		$this->addFrontSystem(new HeavyLaser(5, 8, 6, 300, 60));
		$this->addFrontSystem(new HeavyLaser(5, 8, 6, 300, 60));

		$this->addAftSystem(new HeavyLaser(5, 8, 6, 120, 240));
        $this->addAftSystem(new GraviticThruster(4, 36, 0, 12, 2));
		$this->addAftSystem(new HeavyLaser(5, 8, 6, 120, 240));

        $this->addLeftSystem(new GraviticThruster(4, 18, 0, 6, 3));
		$this->addLeftSystem(new Interdictor(2, 4, 1, 180, 360));
		$this->addLeftSystem(new HeavyLaser(5, 8, 6, 180, 300));
		$this->addLeftSystem(new HeavyLaser(5, 8, 6, 240, 360));

        $this->addRightSystem(new GraviticThruster(4, 18, 0, 6, 4));
		$this->addRightSystem(new Interdictor(2, 4, 1, 0, 180));
		$this->addRightSystem(new HeavyLaser(5, 8, 6, 0, 120));
		$this->addRightSystem(new HeavyLaser(5, 8, 6, 60, 180));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 54));
        $this->addAftSystem(new Structure( 4, 48));
        $this->addPrimarySystem(new Structure( 5, 64 ));
        $this->addLeftSystem(new Structure( 5, 64));
        $this->addRightSystem(new Structure( 5, 64 ));
        
        $this->hitChart = array(
        		0=> array(
        				9 => "Structure",
        				11 => "Jump Engine",
        				13 => "Scanner",
						14 => "Computer",
        				16 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				6 => "Thruster",
        				8 => "Spinal Laser",
        				11 => "Heavy Laser",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				8 => "Heavy Laser",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				6 => "Thruster",
        				8 => "Heavy Laser",
						10 => "Interdictor",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				6 => "Thruster",
        				8 => "Heavy Laser",
						10 => "Interdictor",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}

?>