<?php
class gaimKorvex extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 800;
		$this->faction = "Gaim Intelligence";
		$this->phpclass = "gaimKorvex";
		$this->imagePath = "img/ships/GaimKorvex.png";
		$this->shipClass = "Korvex Battlecruiser";
		$this->shipSizeClass = 3;
		$this->fighters = array("normal"=>12);
		$this->unofficial = true;
	    
        $this->isd = 2266;

		$this->forwardDefense = 18;
		$this->sideDefense = 15;

		$this->turncost = 1;
		$this->turndelaycost = 1;
		$this->accelcost = 4;
		$this->rollcost = 4;
		$this->pivotcost = 3;

		$this->iniativebonus = 0;

		// This unit has thrusters in non-traditional locations
		// It has retro, main, and side thrusters on the port and starboard 
		// It also has a retro in front and a main in aft
		// Port thrusters
		$t1l = new Thruster(4, 6, 0, 2, 1); // port retro
		$t3l = new Thruster(5, 15, 0, 6, 3); // port side
    	$t2l = new Thruster(3, 9, 0, 3, 2); // port main

		//Starboard thrusters
		$t1r = new Thruster(4, 6, 0, 2, 1); // starboard retro
		$t4r = new Thruster(5, 15, 0, 6, 4); // starboard side
		$t2r = new Thruster(3, 9, 0, 3, 2); // starboard main

		//Actual placement of thrusters
		$this->addFrontSystem($t1l);
		$this->addLeftSystem($t3l);
		$this->addAftSystem($t2l);
		$this->addFrontSystem($t1r);
		$this->addRightSystem($t4r);
    	$this->addAftSystem($t2r);

		$this->addPrimarySystem(new Reactor(5, 20, 0, 0));
		$this->addPrimarySystem(new CnC(6, 16, 0, 0));
		$this->addPrimarySystem(new Scanner(6, 20, 4, 9));
		$this->addPrimarySystem(new Engine(5, 12, 0, 12, 4));
		$this->addPrimarySystem(new Hangar(5, 4, 2));
		$this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 180, 360));
		$this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 300, 180));
		$this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 60, 240));

		$this->addFrontSystem(new Thruster(4, 6, 0, 2, 1));
		$this->addFrontSystem(new Bulkhead(0, 3));
		$this->addFrontSystem(new Bulkhead(0, 3));
		$this->addFrontSystem(new ParticleConcentrator(5, 9, 7, 240, 360));
		$this->addFrontSystem(new PacketTorpedo(5, 6, 5, 300, 120));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 240, 120));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 240, 120));

		$this->addAftSystem(new Thruster(5, 9, 0, 6, 2));
		$this->addAftSystem(new Hangar(4, 12, 6));
		$this->addAftSystem(new Bulkhead(0, 3));
		$this->addAftSystem(new Bulkhead(0, 3));
		$this->addAftSystem(new JumpEngine(4, 20, 5, 20));
		
		$this->addLeftSystem(new Bulkhead(0, 4));
		$this->addLeftSystem(new Bulkhead(0, 4));
		$this->addLeftSystem(new BattleLaser(5, 6, 6, 240, 360));
		$this->addLeftSystem(new PacketTorpedo(5, 6, 5, 240, 360));
        $this->addLeftSystem(new TwinArray(3, 6, 2, 180, 60));

		$this->addRightSystem(new Bulkhead(0, 4));
		$this->addRightSystem(new Bulkhead(0, 4));
		$this->addRightSystem(new BattleLaser(4, 6, 6, 0, 120));
		$this->addRightSystem(new PacketTorpedo(4, 6, 5, 0, 120));
        $this->addRightSystem(new TwinArray(3, 6, 2, 300, 180));
        
        $this->addFrontSystem(new Structure( 6, 56));
        $this->addAftSystem(new Structure( 4, 48));
        $this->addLeftSystem(new Structure( 6, 56));
        $this->addRightSystem(new Structure( 5, 72));
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
                        6 => "Particle Concentrator",
                        8 => "Packet Torpedo",
						10 => "Twin Array",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        8 => "Thruster",
                        10 => "Hangar",
						12 => "Jumpe Engine",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        2 => "1:Thruster",
                        4 => "3:Thruster",
                        6 => "2:Thruster",
                        8 => "Twin Array",
						10 => "Packet Torpedo",
						12 => "Battle Laser",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        2 => "1:Thruster",
                        4 => "4:Thruster",
                        6 => "2:Thruster",
                        8 => "Twin Array",
						10 => "Packet Torpedo",
						12 => "Battle Laser",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );

    }
}
?>
