<?php
class HyachOkathKat extends MediumShip{
	public $HyachSpecialists;    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 600;
		$this->faction = "Hyach Gerontocracy";
		$this->phpclass = "HyachOkathKat";
		$this->imagePath = "img/ships/HyachOkathKat.png";
		$this->shipClass = "Okath Kat Fast Frigate";
		$this->canvasSize = 100;
        $this->gravitic = true;
        $this->agile = true;
        $this->isd = 2215;

		$this->forwardDefense = 13;
		$this->sideDefense = 14;

		$this->turncost = 0.5;
		$this->turndelaycost = 0.5;
		$this->accelcost = 2;
		$this->rollcost = 2;
		$this->pivotcost = 2;
		$this->iniativebonus = 60;
		

		$this->addPrimarySystem(new Reactor(4, 18, 0, 0));
		$this->addPrimarySystem(new CnC(4, 11, 0, 0));
		$sensors = new Scanner(4, 22, 5, 9);
			$sensors->markHyach();
			$this->addPrimarySystem($sensors); 
		$this->addPrimarySystem(new Engine(4, 20, 0, 10, 2));
		$this->addPrimarySystem(new Hangar(4, 1));
		$this->addPrimarySystem(new GraviticThruster(4, 16, 0, 5, 3));
		$this->addPrimarySystem(new GraviticThruster(4, 16, 0, 5, 4));
		$this->addPrimarySystem(new HyachComputer(4, 8, 0, 2));//$armour, $maxhealth, $powerReq, $output		
		$HyachSpecialists = $this->createHyachSpecialists(1); //$specTotal
			$this->addPrimarySystem( $HyachSpecialists );	

		$this->addFrontSystem(new GraviticThruster(4, 15, 0, 6, 1));
		$this->addFrontSystem(new Interdictor(2, 6, 1, 180, 360));
		$this->addFrontSystem(new MediumLaser(3, 6, 5, 240, 360));
		$this->addFrontSystem(new MediumLaser(3, 6, 5, 300, 60));
		$this->addFrontSystem(new MediumLaser(3, 6, 5, 0, 120));
		$this->addFrontSystem(new Interdictor(2, 6, 1, 0, 180));

		$this->addAftSystem(new GraviticThruster(4, 26, 0, 10, 2));
		$this->addAftSystem(new MediumLaser(2, 6, 5, 180, 300));
		$this->addAftSystem(new MediumLaser(2, 6, 5, 60, 180));
	
        $this->addPrimarySystem(new Structure( 4, 72));
		
				$this->hitChart = array(
                0=> array(
                        8 => "Thruster",
                        11 => "Scanner",
						12 => "Hangar",
						14 => "Computer",
                        17 => "Engine",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        5 => "Thruster",
                        8 => "Medium Laser",
                        10 => "Interdictor",
                        17 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        8 => "Medium Laser",
                        17 => "Structure",
                        20 => "Primary",
                ),
        );
    }
}



?>
