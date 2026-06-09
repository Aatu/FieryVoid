<?php
class SshelathVulshara extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 860;
		$this->faction = "Escalation Wars Sshel'ath Alliance";
        $this->phpclass = "SshelathVulshara";
        $this->imagePath = "img/ships/EscalationWars/SshelathVulshara.png";
        $this->shipClass = "Vulshara Heavy Cruiser";
        $this->shipSizeClass = 3;
		$this->canvasSize = 170; //img has 200px per side
		$this->unofficial = true;

		$this->isd = 1986;
		$this->fighters = array("mediuem"=>12, "light"=>6);

        $this->forwardDefense = 16;
        $this->sideDefense = 17;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(6, 23, 0, 0));
        $this->addPrimarySystem(new CnC(6, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 18, 5, 8));
        $this->addPrimarySystem(new Engine(5, 18, 0, 16, 4));
		$this->addPrimarySystem(new Hangar(5, 21, 12));
   
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new EWEMLaser(4, 6, 5, 300, 60));
        $this->addFrontSystem(new EWEMLaser(4, 6, 5, 300, 60));
        $this->addFrontSystem(new EWDefenseLaser2(1, 2, 1, 180, 60));
        $this->addFrontSystem(new EWDefenseLaser2(1, 2, 1, 240, 120));
        $this->addFrontSystem(new EWDefenseLaser2(1, 2, 1, 300, 180));

        $this->addAftSystem(new Thruster(4, 20, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 20, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 20, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 20, 0, 4, 2));
		$this->addAftSystem(new EWGatlingLaser(3, 7, 4, 90, 270));
		$this->addAftSystem(new EWGatlingLaser(3, 7, 4, 90, 270));
		$this->addAftSystem(new JumpEngine(5, 20, 5, 32));
        $this->addAftSystem(new EWEMLaser(4, 6, 5, 180, 300));
        $this->addAftSystem(new EWEMLaser(4, 6, 5, 60, 180));

		$this->addLeftSystem(new EWGatlingLaser(4, 7, 4, 180, 360));
        $this->addLeftSystem(new EWEMTorpedo(4, 6, 5, 240, 360));
        $this->addLeftSystem(new LaserCutter(3, 6, 4, 240, 360));
        $this->addLeftSystem(new LaserCutter(3, 6, 4, 240, 360));
        $this->addLeftSystem(new Thruster(3, 15, 0, 5, 3));

		$this->addRightSystem(new EWGatlingLaser(4, 7, 4, 0, 180));
        $this->addRightSystem(new EWEMTorpedo(4, 6, 5, 0, 120));
        $this->addRightSystem(new LaserCutter(3, 6, 4, 0, 120));
        $this->addRightSystem(new LaserCutter(3, 6, 4, 0, 120));
        $this->addRightSystem(new Thruster(3, 15, 0, 5, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(5, 41));
        $this->addAftSystem(new Structure(4, 50));
        $this->addLeftSystem(new Structure(4, 58));
        $this->addRightSystem(new Structure(4, 58));
        $this->addPrimarySystem(new Structure(5, 50));
		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					11 => "Scanner",
					14 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					3 => "Thruster",
					6 => "EM Laser",
					8 => "Defense Laser II",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					7 => "Thruster",
					9 => "Jump Engine",
					11 => "EM Laser",
					13 => "Gatling Laser",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					6 => "EM Torpedo",
					8 => "Gatling Laser",
					11 => "Laser Cutter",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					6 => "EM Torpedo",
					8 => "Gatling Laser",
					11 => "Laser Cutter",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
