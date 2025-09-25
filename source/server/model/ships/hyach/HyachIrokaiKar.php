<?php
class HyachIrokaiKar extends BaseShip{
	public $HyachSpecialists;    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 950;
	$this->faction = "Hyach Gerontocracy";
        $this->phpclass = "HyachIrokaiKar";
        $this->imagePath = "img/ships/HyachIrokaiKam.png";
		$this->canvasSize = 280;        
        $this->shipClass = "Irokai Kar Strike Cruiser";
			$this->variantOf = 'Irokai Kam Battlecruiser';
			$this->occurence = "uncommon";
        $this->shipSizeClass = 3;
        $this->fighters = array("normal"=>12);

        $this->isd = 2224;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
      
        
        $this->gravitic = true;

        $this->addPrimarySystem(new Reactor(5, 28, 0, 8));
        $this->addPrimarySystem(new CnC(5, 21, 0, 0));
		$sensors = new Scanner(5, 28, 6, 11);
			$sensors->markHyach();
			$this->addPrimarySystem($sensors); 
 //       $this->addPrimarySystem(new Scanner(5, 28, 6, 11));
        $this->addPrimarySystem(new Engine(5, 26, 0, 12, 3));
		$this->addPrimarySystem(new Hangar(4, 2));
		$this->addPrimarySystem(new JumpEngine(5, 21, 4, 20));
		$this->addPrimarySystem(new HyachComputer(5, 15, 0, 3));//$armour, $maxhealth, $powerReq, $output		
		$HyachSpecialists = $this->createHyachSpecialists(2); //$specTotal
			$this->addPrimarySystem( $HyachSpecialists );	

        $this->addFrontSystem(new GraviticThruster(4, 11, 0, 3, 1));
        $this->addFrontSystem(new GraviticThruster(4, 11, 0, 3, 1));
		$this->addFrontSystem(new SpinalLaser(5, 12, 12, 330, 30));
		$this->addFrontSystem(new Hangar(4, 12));
		$this->addFrontSystem(new Maser(2, 6, 3, 300, 60));
		$this->addFrontSystem(new Maser(2, 6, 3, 300, 60));

		$this->addAftSystem(new Interdictor(2, 4, 1, 90, 270));
        $this->addAftSystem(new Maser(2, 6, 3, 90, 270));
        $this->addAftSystem(new GraviticThruster(4, 36, 0, 12, 2));
        $this->addAftSystem(new Maser(2, 6, 3, 90, 270));
		$this->addAftSystem(new Interdictor(2, 4, 1, 90, 270));

        $this->addLeftSystem(new GraviticThruster(4, 18, 0, 6, 3));
		$this->addLeftSystem(new Interdictor(2, 4, 1, 270, 90));
        $this->addLeftSystem(new Maser(2, 6, 3, 240, 360));
		$this->addLeftSystem(new MediumLaser(3, 6, 5, 180, 300));
		$this->addLeftSystem(new MediumLaser(3, 6, 5, 240, 360));

        $this->addRightSystem(new GraviticThruster(4, 18, 0, 6, 4));
		$this->addRightSystem(new Interdictor(2, 4, 1, 270, 90));
        $this->addRightSystem(new Maser(2, 6, 3, 0, 120));
		$this->addRightSystem(new MediumLaser(3, 6, 5, 60, 180));
		$this->addRightSystem(new MediumLaser(3, 6, 5, 0, 120));

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
        				5 => "Thruster",
        				6 => "Spinal Laser",
        				8 => "Hangar",
						10 => "Maser",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				8 => "Maser",
						10 => "Interdictor",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				4 => "Thruster",
        				6 => "Maser",
        				9 => "Medium Laser",
						10 => "Interdictor",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				4 => "Thruster",
        				6 => "Maser",
        				9 => "Medium Laser",
						10 => "Interdictor",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}

?>