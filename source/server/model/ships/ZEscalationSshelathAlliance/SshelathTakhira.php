<?php
class SshelathTakhira extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 375;
        $this->faction = "ZEscalation Sshel'ath Alliance";
        $this->phpclass = "SshelathTakhira";
        $this->imagePath = "img/ships/EscalationWars/SshelathTasholn.png";
        $this->shipClass = "Takhira Destroyer";
			$this->variantOf = "Tasholn Destroyer";
			$this->occurence = "rare";		
	    $this->isd = 1971;
			$this->unofficial = true;
        $this->canvasSize = 110;
		
        $this->forwardDefense = 14;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;

        $this->addPrimarySystem(new Reactor(4, 13, 0, -4));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 3, 6));
		$this->addPrimarySystem(new LaserCutter(3, 6, 4, 300, 60));
        $this->addPrimarySystem(new Engine(4, 14, 0, 8, 3));
		$this->addPrimarySystem(new Hangar(3, 1));
        $this->addPrimarySystem(new Thruster(2, 8, 0, 3, 1));
        $this->addPrimarySystem(new Thruster(2, 8, 0, 3, 1));
        $this->addPrimarySystem(new Thruster(2, 12, 0, 4, 2));
        $this->addPrimarySystem(new Thruster(2, 12, 0, 4, 2));

        $this->addLeftSystem(new LaserCutter(2, 6, 4, 240, 360));
		$this->addLeftSystem(new EWGatlingLaser(2, 7, 4, 180, 60));
        $this->addLeftSystem(new Thruster(3, 11, 0, 4, 3));

        $this->addRightSystem(new LaserCutter(2, 6, 4, 0, 120));
		$this->addRightSystem(new EWGatlingLaser(2, 7, 4, 300, 180));
        $this->addRightSystem(new Thruster(3, 11, 0, 4, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(4, 48));
        $this->addLeftSystem(new Structure(3, 40));
        $this->addRightSystem(new Structure(3, 40));
        
        $this->hitChart = array(
        		0=> array(
        				7 => "Structure",
						9 => "Laser Cutter",
        				12 => "Thruster",
        				14 => "Scanner",
        				16 => "Engine",
						17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		3=> array(
        				4 => "Thruster",
        				6 => "Laser Cutter",
        				9 => "Gatling Laser",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				4 => "Thruster",
        				6 => "Laser Cutter",
        				9 => "Gatling Laser",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
?>
