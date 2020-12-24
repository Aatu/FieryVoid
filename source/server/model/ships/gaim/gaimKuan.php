<?php
class gaimKuan extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 500;
		$this->faction = "Gaim";
        $this->phpclass = "gaimKuan";
        $this->imagePath = "img/ships/GaimKuach.png";
        $this->shipClass = "Kuan Corvette";
			$this->variantOf = "Kuach Minesweeping Corvette";
			$this->occurence = "common";        
		$this->isd = 2255;
		
        $this->forwardDefense = 13;
        $this->sideDefense = 12;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;

        $this->addPrimarySystem(new Reactor(5, 12, 0, 6));
        $this->addPrimarySystem(new CnC(5, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 13, 4, 8));
        $this->addPrimarySystem(new Engine(5, 11, 0, 8, 2));
        $this->addPrimarySystem(new Hangar(4, 1));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 4, 1));
        $this->addPrimarySystem(new Thruster(5, 19, 0, 8, 2));
        $this->addPrimarySystem(new ScatterGun(4, 8, 3, 240, 120));

        $this->addLeftSystem(new PacketTorpedo(4, 6, 5, 240, 360));
        $this->addLeftSystem(new StdParticleBeam(3, 4, 1, 240, 60));
        $this->addLeftSystem(new MediumPulse(4, 6, 3, 240, 360));
        $this->addLeftSystem(new Thruster(4, 13, 0, 4, 3));
		$this->addLeftSystem(new Bulkhead(0, 4));

        $this->addRightSystem(new PacketTorpedo(4, 6, 5, 0, 120));
        $this->addRightSystem(new StdParticleBeam(3, 4, 1, 300, 120));
        $this->addRightSystem(new MediumPulse(4, 6, 3, 0, 120));
        $this->addRightSystem(new Thruster(4, 13, 0, 4, 4));
		$this->addRightSystem(new Bulkhead(0, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(5, 32));
        $this->addLeftSystem(new Structure(4, 40));
        $this->addRightSystem(new Structure(4, 40));
		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					11 => "Thruster",
					12 => "Scattergun",
					14 => "Scanner",
					16 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			3=> array(
					3 => "Thruster",
					5 => "Packet Torpedo",
					7 => "Medium Pulse Cannon",
					9 => "Standard Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					3 => "Thruster",
					5 => "Packet Torpedo",
					7 => "Medium Pulse Cannon",
					9 => "Standard Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
		);
		
    }
}
