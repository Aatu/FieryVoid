<?php
class HyachSenchlatKam extends HeavyCombatVessel{
    public $HyachSpecialists;
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 800;
        $this->faction = "Hyach Gerontocracy";
        $this->phpclass = "HyachSenchlatKam";
        $this->imagePath = "img/ships/HyachSenchlatKam.png";      
        $this->shipClass = "Senchlat Kam Light Cruiser";
        $this->gravitic = true;

        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;
		
        $this->isd = 2217;
      

        $this->addPrimarySystem(new Reactor(4, 21, 0, 0));
        $this->addPrimarySystem(new CnC(4, 15, 0, 0));
		$sensors = new Scanner(4, 26, 6, 10);
			$sensors->markHyach();
			$this->addPrimarySystem($sensors); 
        $this->addPrimarySystem(new Engine(4, 20, 0, 10, 2));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new GraviticThruster(4, 18, 0, 5, 3));
        $this->addPrimarySystem(new GraviticThruster(4, 18, 0, 5, 4));
		$this->addPrimarySystem(new HyachComputer(4, 15, 0, 3));//$armour, $maxhealth, $powerReq, $output        
		$HyachSpecialists = $this->createHyachSpecialists(2); //$specTotal
			$this->addPrimarySystem( $HyachSpecialists );	

        $this->addFrontSystem(new GraviticThruster(4, 9, 0, 3, 1));
        $this->addFrontSystem(new BlastLaser(3, 10, 5, 240, 60));
        $this->addFrontSystem(new BlastLaser(3, 10, 5, 300, 60));
        $this->addFrontSystem(new Interdictor(2, 4, 1, 270, 90));
        $this->addFrontSystem(new Maser(2, 6, 3, 270, 90));
        $this->addFrontSystem(new BlastLaser(3, 10, 5, 300, 60));
        $this->addFrontSystem(new BlastLaser(3, 10, 5, 300, 120));
        $this->addFrontSystem(new GraviticThruster(4, 9, 0, 3, 1));

        $this->addAftSystem(new BlastLaser(3, 10, 5, 120, 300));
        $this->addAftSystem(new Maser(2, 6, 3, 90, 270));
        $this->addAftSystem(new GraviticThruster(4, 32, 0, 10, 2));
        $this->addAftSystem(new Interdictor(2, 4, 1, 90, 270));
        $this->addAftSystem(new BlastLaser(3, 10, 5, 60, 240));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 64));
        $this->addAftSystem(new Structure( 4, 48));
        $this->addPrimarySystem(new Structure( 4, 72));
		
		$this->hitChart = array(
            0=> array(
                    8 => "Structure",
					10 => "Thruster",
					13 => "Scanner",
					14 => "Hangar",
					15 => "Computer",
                    17 => "Engine",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    5 => "Thruster",
					7 => "Blast Laser",
                    8 => "Maser",
                    9 => "Interdictor",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    5 => "Thruster",
					7 => "Blast Laser",
                    8 => "Maser",
					9 => "Interdictor",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
