<?php
class SshelathNakesra extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 600;
	$this->faction = "ZEscalation Sshel'ath Alliance";
        $this->phpclass = "SshelathNakesra";
        $this->imagePath = "img/ships/EscalationWars/SshelathNasarra.png";
        $this->shipClass = "Nakesra Battlecruiser";
			$this->variantOf = "Nasarra Attack Cruiser";
			$this->occurence = "rare";		
        $this->shipSizeClass = 3;
		$this->canvasSize = 160; //img has 200px per side
		$this->unofficial = true;

 		$this->isd = 1974;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 1.0;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 10;
        
        $this->addPrimarySystem(new Reactor(4, 20, 0, 0));
        $this->addPrimarySystem(new CnC(4, 14, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 13, 3, 6));
        $this->addPrimarySystem(new Engine(4, 16, 0, 12, 4));
		$this->addPrimarySystem(new Hangar(3, 1));
		
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
		$this->addFrontSystem(new NexusHeavyLaserCutter(3, 8, 5, 300, 60));
		$this->addFrontSystem(new NexusHeavyLaserCutter(3, 8, 5, 300, 60));

        $this->addAftSystem(new Thruster(3, 20, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 20, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 20, 0, 4, 2));

        $this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
		$this->addLeftSystem(new NexusHeavyLaserCutter(3, 8, 5, 240, 360));
		$this->addLeftSystem(new NexusHeavyLaserCutter(3, 8, 5, 240, 360));
        $this->addLeftSystem(new Thruster(3, 15, 0, 5, 3));

        $this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new NexusHeavyLaserCutter(3, 8, 5, 0, 120));
		$this->addRightSystem(new NexusHeavyLaserCutter(3, 8, 5, 0, 120));
        $this->addRightSystem(new Thruster(3, 15, 0, 5, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 36));
        $this->addAftSystem(new Structure(4, 36));
        $this->addLeftSystem(new Structure(4, 36));
        $this->addRightSystem(new Structure(4, 36));
        $this->addPrimarySystem(new Structure(4, 40));
		
		$this->hitChart = array(
			0=> array(
					10 => "Structure",
					13 => "Scanner",
					16 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					5 => "Thruster",
					7 => "Light Particle Beam",
					10 => "Heavy Laser Cutter",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					9 => "Thruster",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					5 => "Thruster",
					7 => "Light Particle Beam",
					10 => "Heavy Laser Cutter",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					7 => "Light Particle Beam",
					10 => "Heavy Laser Cutter",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
