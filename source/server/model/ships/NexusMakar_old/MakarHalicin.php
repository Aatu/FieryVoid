<?php
class MakarHalicin extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 550;
	$this->faction = "Nexus Makar Federation (early)";
        $this->phpclass = "MakarHalicin";
        $this->imagePath = "img/ships/Nexus/makar_halicin2.png";
        $this->shipClass = "Halicin Cruiser";
        $this->shipSizeClass = 3;
		$this->canvasSize = 170; 
		$this->unofficial = true;

		$this->isd = 1932;

		$this->critRollMod += 1;
	    $this->notes = '<br>Unreliable Ship:';
 	    $this->notes .= '<br> - Sensor Fluctuations';
 	    $this->notes .= '<br> - Sluggish';
// 	    $this->notes .= '<br> - Vulnerable to Criticals';
		$this->enhancementOptionsDisabled[] = 'VULN_CRIT';
		$this->enhancementOptionsDisabled[] = 'SLUGGISH';
		$this->enhancementOptionsDisabled[] = 'IMPR_SENS';
        
        $this->forwardDefense = 17;
        $this->sideDefense = 17;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 4;
        $this->pivotcost = 4;
        $this->iniativebonus = -7;
        
        $this->addPrimarySystem(new Reactor(4, 24, 0, 0));
        $this->addPrimarySystem(new CnC(3, 20, 0, 0));
 		$sensors = new Scanner(3, 16, 4, 6);
			$sensors->markSensorFlux();
			$this->addPrimarySystem($sensors);
		$this->addPrimarySystem(new Hangar(0, 6));
		
        $this->addFrontSystem(new Thruster(2, 12, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 12, 0, 2, 1));
		$this->addFrontSystem(new EWRocketLauncher(2, 4, 1, 270, 90));
		$this->addFrontSystem(new EWHeavyRocketLauncher(3, 6, 2, 300, 60));
		$this->addFrontSystem(new EWRocketLauncher(2, 4, 1, 270, 90));
        $this->addFrontSystem(new ConnectionStrut(3));
        $this->addFrontSystem(new Thruster(2, 12, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 12, 0, 2, 1));

        $this->addAftSystem(new Thruster(2, 12, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 12, 0, 2, 2));
        $this->addAftSystem(new Engine(4, 16, 0, 8, 4));
		$this->addAftSystem(new PlasmaTorch(1, 4, 2, 180, 360));
		$this->addAftSystem(new EWRocketLauncher(2, 4, 1, 90, 270));
		$this->addAftSystem(new EWHeavyRocketLauncher(3, 6, 2, 120, 240));
		$this->addAftSystem(new EWRocketLauncher(2, 4, 1, 90, 270));
		$this->addAftSystem(new PlasmaTorch(1, 4, 2, 0, 180));
        $this->addAftSystem(new ConnectionStrut(3));
        $this->addAftSystem(new Thruster(2, 12, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 12, 0, 2, 2));

		$this->addLeftSystem(new NexusDefenseGun(2, 4, 1, 120, 360));
		$this->addLeftSystem(new NexusDefenseGun(2, 4, 1, 180, 60));
		$this->addLeftSystem(new NexusDefenseGun(2, 4, 1, 180, 60));
		$this->addLeftSystem(new EWHeavyRocketLauncher(3, 6, 2, 240, 360));
        $this->addLeftSystem(new ConnectionStrut(3));
        $this->addLeftSystem(new Thruster(3, 15, 0, 5, 3));

		$this->addRightSystem(new NexusDefenseGun(2, 4, 1, 0, 240));
		$this->addRightSystem(new NexusDefenseGun(2, 4, 1, 300, 180));
		$this->addRightSystem(new NexusDefenseGun(2, 4, 1, 300, 180));
		$this->addRightSystem(new EWHeavyRocketLauncher(3, 6, 2, 0, 120));
        $this->addRightSystem(new ConnectionStrut(3));
        $this->addRightSystem(new Thruster(3, 15, 0, 5, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(3, 75));
        $this->addAftSystem(new Structure(3, 68));
        $this->addLeftSystem(new Structure(3, 80));
        $this->addRightSystem(new Structure(3, 80));
        $this->addPrimarySystem(new Structure(3, 72));
		
		$this->hitChart = array(
			0=> array(
					12 => "Structure",
					15 => "Scanner",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					5 => "Thruster",
					8 => "Heavy Rocket Launcher",
					10 => "Rocket Launcher",
					16 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
			2=> array(
					4 => "Thruster",
					6 => "Heavy Rocket Launcher",
					8 => "Rocket Launcher",
					10 => "Plasma Torch",
					12 => "Engine",
					16 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
			3=> array(
					5 => "Thruster",
					8 => "Heavy Rocket Launcher",
					10 => "Defense Gun",
					16 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					8 => "Heavy Rocket Launcher",
					10 => "Defense Gun",
					16 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
		);
    }
}

?>
