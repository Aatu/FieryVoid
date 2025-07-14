<?php
class HyachTachilaKor extends BaseShip{
	public $HyachSpecialists;    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 1025;
	$this->faction = "Hyach Gerontocracy";
        $this->phpclass = "HyachTachilaKor";
        $this->imagePath = "img/ships/HyachTachilaKor.png";
        $this->shipClass = "Tachila Kor Scout Carrier";
        $this->shipSizeClass = 3;
        $this->limited = 33;

        $this->fighters = array("normal"=>24);

        $this->isd = 2215;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        $this->iniativebonus = 0;
        
        $this->gravitic = true;
      

        $this->addPrimarySystem(new Reactor(5, 25, 0, 0));
        $this->addPrimarySystem(new CnC(5, 19, 0, 0));
		$sensors = new ElintScanner(5, 34, 7, 13);
			$sensors->markHyachELINT();
			$this->addPrimarySystem($sensors); 
 //       $this->addPrimarySystem(new Scanner(5, 28, 6, 11));
        $this->addPrimarySystem(new Engine(5, 22, 0, 9, 3));
		$this->addPrimarySystem(new JumpEngine(5, 21, 4, 20));
		$this->addPrimarySystem(new HyachComputer(5, 10, 0, 2));//$armour, $maxhealth, $powerReq, $output			
		$HyachSpecialists = $this->createHyachSpecialists(2); //$specTotal
			$this->addPrimarySystem( $HyachSpecialists );	

        $this->addFrontSystem(new GraviticThruster(4, 11, 0, 3, 1));
        $this->addFrontSystem(new GraviticThruster(4, 11, 0, 3, 1));
		$this->addFrontSystem(new BlastLaser(3, 10, 5, 300, 60));
		$this->addFrontSystem(new Hangar(4, 26));
		$this->addFrontSystem(new Maser(2, 6, 3, 270, 90));
		$this->addFrontSystem(new Maser(2, 6, 3, 270, 90));
		$this->addFrontSystem(new Interdictor(2, 4, 1, 270, 90));

        $this->addAftSystem(new Maser(2, 6, 3, 90, 270));
        $this->addAftSystem(new GraviticThruster(4, 32, 0, 9, 2));
        $this->addAftSystem(new Maser(2, 6, 3, 90, 270));

        $this->addLeftSystem(new GraviticThruster(4, 18, 0, 4, 3));
		$this->addLeftSystem(new Interdictor(2, 4, 1, 180, 360));
        $this->addLeftSystem(new Maser(2, 6, 3, 240, 60));

        $this->addRightSystem(new GraviticThruster(4, 18, 0, 4, 4));
		$this->addRightSystem(new Interdictor(2, 4, 1, 0, 180));
        $this->addRightSystem(new Maser(2, 6, 3, 300, 120));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 48));
        $this->addAftSystem(new Structure( 4, 44));
        $this->addPrimarySystem(new Structure( 5, 60 ));
        $this->addLeftSystem(new Structure( 4, 60));
        $this->addRightSystem(new Structure( 4, 60 ));
        
        $this->hitChart = array(
        		0=> array(
        				9 => "Structure",
        				11 => "Jump Engine",
        				14 => "ELINT Scanner",
						15 => "Computer",
        				17 => "Engine",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				5 => "Thruster",
        				6 => "Blast Laser",
        				8 => "Maser",
						10 => "Hangar",
						11 => "Interdictor",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				8 => "Maser",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				5 => "Thruster",
        				7 => "Maser",
						9 => "Interdictor",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				5 => "Thruster",
        				7 => "Maser",
						9 => "Interdictor",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}

?>