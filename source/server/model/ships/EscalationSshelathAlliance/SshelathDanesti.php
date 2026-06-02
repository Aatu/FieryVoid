<?php
class SshelathDanesti extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 900;
	$this->faction = "Escalation Wars Sshel'ath Alliance";
        $this->phpclass = "SshelathDanesti";
        $this->imagePath = "img/ships/EscalationWars/SshelathDanesti.png";
        $this->shipClass = "Danesti Battlecruiser";
			$this->limited = 10;
        $this->shipSizeClass = 3;
		$this->canvasSize = 170; //img has 200px per side
		$this->unofficial = true;

		$this->isd = 1978;
		$this->fighters = array("normal"=>6);
	
        $this->forwardDefense = 17;
        $this->sideDefense = 17;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(5, 20, 0, 0));
        $this->addPrimarySystem(new CnC(6, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 15, 4, 8));
        $this->addPrimarySystem(new Engine(5, 18, 0, 12, 4));
		$this->addPrimarySystem(new Hangar(5, 8, 6));
   
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new LaserCutter(3, 6, 4, 300, 60));
        $this->addFrontSystem(new LaserCutter(3, 6, 4, 300, 60));
        $this->addFrontSystem(new EWElectronPolarizer(3, 8, 5, 300, 60));
        $this->addFrontSystem(new EWElectronPolarizer(3, 8, 5, 300, 60));

        $this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 15, 0, 6, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
		$this->addAftSystem(new EWGatlingLaser(2, 7, 4, 90, 270));
		$this->addAftSystem(new EWGatlingLaser(2, 7, 4, 90, 270));
		$this->addAftSystem(new EWGatlingLaser(2, 7, 4, 90, 270));
		$this->addAftSystem(new EWGatlingLaser(2, 7, 4, 90, 270));
		$this->addAftSystem(new JumpEngine(4, 15, 5, 32));

		$this->addLeftSystem(new EWGatlingLaser(2, 7, 4, 270, 90));
		$this->addLeftSystem(new EWGatlingLaser(2, 7, 4, 270, 90));
        $this->addLeftSystem(new Thruster(3, 15, 0, 5, 3));

		$this->addRightSystem(new EWGatlingLaser(2, 7, 4, 270, 90));
		$this->addRightSystem(new EWGatlingLaser(2, 7, 4, 270, 90));
        $this->addRightSystem(new Thruster(3, 15, 0, 5, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 48));
        $this->addAftSystem(new Structure(4, 48));
        $this->addLeftSystem(new Structure(4, 50));
        $this->addRightSystem(new Structure(4, 50));
        $this->addPrimarySystem(new Structure(5, 60));
		
		$this->hitChart = array(
			0=> array(
					10 => "Structure",
					12 => "Scanner",
					15 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					7 => "Electron Polarizer",
					9 => "Laser Cutter",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					9 => "Gatling Laser",
					11 => "Jump Engine",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					5 => "Thruster",
					8 => "Gatling Laser",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					8 => "Gatling Laser",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
