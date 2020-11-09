<?php
class gaimGeun extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 700;
		$this->faction = 'Gaim';
		$this->phpclass = "gaimGeun";
		$this->imagePath = "img/ships/GaimGeun.png";
		$this->shipClass = "Geun Defender";
		$this->shipSizeClass = 3;
		$this->fighters = array("normal"=>12);
			$this->limited = 33;
	    
        $this->isd = 2256;

		$this->forwardDefense = 16;
		$this->sideDefense = 16;

		$this->turncost = 1;
		$this->turndelaycost = 1;
		$this->accelcost = 5;
		$this->rollcost = 3;
		$this->pivotcost = 4;

		$this->iniativebonus = -10;

		$this->addPrimarySystem(new Reactor(5, 20, 0, 0));
		$this->addPrimarySystem(new CnC(6, 16, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 16, 3, 6));
		$this->addPrimarySystem(new Engine(5, 11, 0, 5, 4));
		$this->addPrimarySystem(new Hangar(5, 14));
		$this->addPrimarySystem(new StdParticleBeam(3, 4, 1, 0, 360));
		$this->addPrimarySystem(new StdParticleBeam(3, 4, 1, 0, 360));

		$this->addFrontSystem(new Thruster(4, 20, 0, 5, 1));
		$this->addFrontSystem(new ParticleConcentrator(4, 9, 7, 300, 60));
		$this->addFrontSystem(new ParticleConcentrator(4, 9, 7, 300, 60));
		$this->addFrontSystem(new PacketTorpedo(4, 6, 5, 240, 60));
		$this->addFrontSystem(new PacketTorpedo(4, 6, 5, 300, 120));
		$this->addFrontSystem(new Bulkhead(0, 2));
		$this->addFrontSystem(new Bulkhead(0, 2));

		$this->addAftSystem(new Thruster(4, 20, 0, 5, 2));
		$this->addAftSystem(new PacketTorpedo(3, 6, 5, 120, 240));
		$this->addAftSystem(new PacketTorpedo(3, 6, 5, 120, 240));
		$this->addAftSystem(new Bulkhead(0, 2));
		$this->addAftSystem(new Bulkhead(0, 2));

		$this->addLeftSystem(new Thruster(4, 15, 0, 3, 3));
		$this->addLeftSystem(new ScatterGun(4, 8, 3, 180, 360));
		$this->addLeftSystem(new ScatterGun(4, 8, 3, 180, 360));
		$this->addLeftSystem(new Bulkhead(0, 2));
		$this->addLeftSystem(new Bulkhead(0, 2));

		$this->addRightSystem(new Thruster(4, 15, 0, 3, 4));
		$this->addRightSystem(new ScatterGun(4, 8, 3, 0, 180));
		$this->addRightSystem(new ScatterGun(4, 8, 3, 0, 180));
		$this->addRightSystem(new Bulkhead(0, 2));
		$this->addRightSystem(new Bulkhead(0, 2));
        
        $this->addFrontSystem(new Structure( 5, 52));
        $this->addAftSystem(new Structure( 5, 40));
        $this->addLeftSystem(new Structure( 5, 60));
        $this->addRightSystem(new Structure( 5, 60));
        $this->addPrimarySystem(new Structure( 6, 60));
		
		
		$this->hitChart = array(
                0=> array(
                        9 => "Structure",
                        11 => "Standard Particle Beam",
                        13 => "Scanner",
                        15 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        7 => "Particle Concentrator",
                        10 => "Packet Torpedo",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        9 => "Packet Torpedo",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
                        9 => "Scattergun",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
                        9 => "Scattergun",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );

    }
}
?>
