<?php
class HyachUruthaKal extends BaseShip{
	public $HyachSpecialists;    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 1300;
	$this->faction = "Hyach Gerontocracy";
        $this->phpclass = "HyachUruthaKal";
        $this->imagePath = "img/ships/HyachUruthaKal.png";
        $this->shipClass = "Urutha Kal Dreadnought";
        $this->shipSizeClass = 3;
        $this->limited = 33;

        $this->fighters = array("normal"=>12);
		$this->canvasSize = 280;
        $this->isd = 2207;
        
        $this->forwardDefense = 17;
        $this->sideDefense = 17;
        
        $this->turncost = 1.5;
        $this->turndelaycost = 1.5;
        $this->accelcost = 5;
        $this->rollcost = 3;
        $this->pivotcost = 4;
        $this->iniativebonus = 0;
        
        $this->gravitic = true;
             

        $this->addPrimarySystem(new Reactor(5, 30, 0, 0));
        $this->addPrimarySystem(new CnC(6, 25, 0, 0));
		$sensors = new Scanner(5, 30, 6, 12);
			$sensors->markHyach();
			$this->addPrimarySystem($sensors); 

        $this->addPrimarySystem(new Engine(5, 28, 0, 13, 3));
		$this->addPrimarySystem(new Hangar(4, 14));
		$this->addPrimarySystem(new JumpEngine(5, 25, 4, 20));
		$this->addPrimarySystem(new HyachComputer(5, 20, 0, 4));//$armour, $maxhealth, $powerReq, $output
		$HyachSpecialists = $this->createHyachSpecialists(3); //$specTotal
			$this->addPrimarySystem( $HyachSpecialists );						

        $this->addFrontSystem(new GraviticThruster(5, 15, 0, 5, 1));
        $this->addFrontSystem(new GraviticThruster(5, 15, 0, 5, 1));
		$this->addFrontSystem(new Interdictor(2, 4, 1, 270, 90));
		$this->addFrontSystem(new BlastLaser(3, 10, 5, 300, 60));
		$this->addFrontSystem(new SpinalLaser(6, 12, 12, 330, 30));
		$this->addFrontSystem(new BlastLaser(3, 10, 5, 300, 60));
		$this->addFrontSystem(new Interdictor(2, 4, 1, 270, 90));

		$this->addAftSystem(new Maser(2, 6, 3, 90, 270));
		$this->addAftSystem(new MediumLaser(3, 6, 5, 180, 300));
        $this->addAftSystem(new GraviticThruster(4, 40, 0, 13, 2));
		$this->addAftSystem(new MediumLaser(3, 6, 5, 60, 180));
		$this->addAftSystem(new Maser(2, 6, 3, 90, 270));

        $this->addLeftSystem(new GraviticThruster(4, 20, 0, 6, 3));
		$this->addLeftSystem(new Interdictor(2, 4, 1, 180, 360));
		$this->addLeftSystem(new MediumLaser(3, 6, 5, 300, 60));
		$this->addLeftSystem(new MediumLaser(3, 6, 5, 240, 360));
		$this->addLeftSystem(new Maser(3, 6, 3, 240, 360));
		$this->addLeftSystem(new Maser(2, 6, 3, 240, 360));

        $this->addRightSystem(new GraviticThruster(4, 20, 0, 6, 4));
		$this->addRightSystem(new Interdictor(2, 4, 1, 0, 180));
		$this->addRightSystem(new MediumLaser(3, 6, 5, 300, 60));
		$this->addRightSystem(new MediumLaser(3, 6, 5, 0, 120));
		$this->addRightSystem(new Maser(3, 6, 3, 0, 120));
		$this->addRightSystem(new Maser(2, 6, 3, 0, 120));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 66));
        $this->addAftSystem(new Structure( 4, 60));
        $this->addPrimarySystem(new Structure( 5, 80));
        $this->addLeftSystem(new Structure( 5, 80));
        $this->addRightSystem(new Structure( 5, 80 ));
        
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
        				4 => "Thruster",
        				6 => "Spinal Laser",
        				8 => "Blast Laser",
						10 => "Interdictor",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				8 => "Maser",
						10 => "Medium Laser",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				4 => "Thruster",
						7 => "Maser",
        				9 => "Medium Laser",
						10 => "Interdictor",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				4 => "Thruster",
						7 => "Maser",
        				9 => "Medium Laser",
						10 => "Interdictor",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}

?>