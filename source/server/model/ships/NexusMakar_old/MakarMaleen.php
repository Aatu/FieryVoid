<?php
class MakarMaleen extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 450;
        $this->faction = "Nexus Makar Federation (early)";
        $this->phpclass = "MakarMaleen";
        $this->imagePath = "img/ships/Nexus/makar_maleen2.png";
		$this->canvasSize = 120; //img has 200px per side
        $this->shipClass = "Maleen Heavy Frigate";
		$this->unofficial = true;
        $this->isd = 1928;

		$this->critRollMod += 1;
	    $this->notes = '<br>Unreliable Ship:';
 	    $this->notes .= '<br> - Sensor Fluctuations';
 	    $this->notes .= '<br> - Sluggish';
// 	    $this->notes .= '<br> - Vulnerable to Criticals';
		$this->enhancementOptionsDisabled[] = 'VULN_CRIT';
		$this->enhancementOptionsDisabled[] = 'SLUGGISH';
		$this->enhancementOptionsDisabled[] = 'IMPR_SENS';

        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 0.75;
        $this->turndelaycost = 0.75;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 23;
         
        $this->addPrimarySystem(new Reactor(4, 18, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
		$sensors = new Scanner(3, 16, 4, 6);
			$sensors->markSensorFlux();
			$this->addPrimarySystem($sensors);
        $this->addPrimarySystem(new Hangar(0, 2));
		$this->addPrimarySystem(new NexusDefenseGun(1, 4, 1, 0, 360));
		$this->addPrimarySystem(new NexusDefenseGun(1, 4, 1, 0, 360));
        $this->addPrimarySystem(new Thruster(2, 13, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(2, 13, 0, 4, 4));
      
        $this->addFrontSystem(new Thruster(3, 12, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 12, 0, 2, 1));
		$this->addFrontSystem(new EWRocketLauncher(3, 4, 1, 240, 60));
		$this->addFrontSystem(new EWHeavyRocketLauncher(3, 6, 2, 240, 360));
		$this->addFrontSystem(new EWHeavyRocketLauncher(3, 6, 2, 300, 60));
		$this->addFrontSystem(new EWHeavyRocketLauncher(3, 6, 2, 0, 120));
		$this->addFrontSystem(new EWRocketLauncher(3, 4, 1, 300, 120));
        $this->addFrontSystem(new ConnectionStrut(3));
                
        $this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
        $this->addAftSystem(new Engine(4, 16, 0, 6, 4));
		$this->addAftSystem(new NexusDefenseGun(1, 4, 1, 180, 360));
		$this->addAftSystem(new PlasmaTorch(1, 4, 2, 180, 360));
		$this->addAftSystem(new PlasmaTorch(1, 4, 2, 0, 180));
		$this->addAftSystem(new NexusDefenseGun(1, 4, 1, 0, 180));
        $this->addAftSystem(new ConnectionStrut(3));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 75));
        $this->addAftSystem(new Structure( 3, 68));
        $this->addPrimarySystem(new Structure( 3, 72));
		
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
					10 => "Defense Gun",
                    12 => "Thruster",
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
                    6 => "Defense Gun",
					8 => "Plasma Torch",
					10 => "Engine",
                    16 => "Structure",
					18 => "Connection Strut",
                    20 => "Primary",
            ),
        );
    }
}
?>
