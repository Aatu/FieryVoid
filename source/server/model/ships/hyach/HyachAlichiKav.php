<?php
class HyachAlichiKav extends HeavyCombatVessel{

    public $submarine = true;

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 700;
        $this->faction = "Hyach Gerontocracy";
        $this->phpclass = "HyachAlichiKav";
        $this->imagePath = "img/ships/HyachAlichiKav.png";
        $this->shipClass = "Alichi Kav Stealth Cruiser";
		$this->canvasSize = 100;
        $this->gravitic = true;
        $this->limited = 33;
        $this->notes = "Stealth ship (see FAQ)";
        $this->notes .= "<br>Costs +33% to turn when moving in reverse";        
		
        $this->forwardDefense = 10;
        $this->sideDefense = 12;
        $this->turncost = 1;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 30;
      
		
        $this->isd = 2210;

        $this->addPrimarySystem(new Reactor(6, 18, 0, 0));
        $this->addPrimarySystem(new CnC(6, 15, 0, 0));
		$sensors = new Scanner(6, 20, 5, 6);
			$sensors->markHyach();
			$this->addPrimarySystem($sensors); 
        $this->addPrimarySystem(new Engine(6, 20, 0, 10, 2));
        $this->addPrimarySystem(new Hangar(3, 1));
		$this->addPrimarySystem(new Stealth(1,1,0));
		$this->addPrimarySystem(new Interdictor(2, 4, 1, 180, 360));
		$this->addPrimarySystem(new MediumLaser(3, 6, 5, 240, 360));
		$this->addPrimarySystem(new MediumLaser(3, 6, 5, 0, 120));
		$this->addPrimarySystem(new Interdictor(2, 4, 1, 0, 180));
		$this->addPrimarySystem(new HyachComputer(5, 10, 0, 2));//$armour, $maxhealth, $powerReq, $output			
		$HyachSpecialists = $this->createHyachSpecialists(1); //$specTotal
			$this->addPrimarySystem( $HyachSpecialists );	

        $this->addFrontSystem(new GraviticThruster(4, 15, 0, 5, 1));
		$this->addFrontSystem(new SpinalLaser(5, 12, 12, 330, 30));
		$this->addFrontSystem(new MediumLaser(3, 6, 5, 240, 360));
		$this->addFrontSystem(new MediumLaser(3, 6, 5, 0, 120));

        $this->addAftSystem(new GraviticThruster(4, 15, 0, 5, 3));
        $this->addAftSystem(new GraviticThruster(4, 28, 0, 10, 2));        
        $this->addAftSystem(new GraviticThruster(4, 15, 0, 5, 4));


        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 48));
        $this->addAftSystem(new Structure( 5, 48));
        $this->addPrimarySystem(new Structure( 6, 72));
		
		$this->hitChart = array(
            0=> array(
                    8 => "Structure",
                    10 => "Medium Laser",
                    12 => "Interdictor",
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
                    8 => "Medium Laser",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    9 => "Thruster",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
