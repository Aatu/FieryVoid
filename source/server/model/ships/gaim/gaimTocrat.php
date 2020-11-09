<?php
class gaimTocrat extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 425;
		$this->faction = 'Gaim';
		$this->phpclass = "gaimTocrat";
		$this->imagePath = "img/ships/GaimTocrat.png";
		$this->shipClass = "Tocrat Supply Barge";
		$this->shipSizeClass = 3;
		$this->fighters = array("light"=>12);
	    
        $this->isd = 2251;

		$this->forwardDefense = 15;
		$this->sideDefense = 16;

		$this->turncost = 1.33;
		$this->turndelaycost = 1.33;
		$this->accelcost = 4;
		$this->rollcost = 3;
		$this->pivotcost = 3;

		$this->iniativebonus = -30;

		$this->addPrimarySystem(new Reactor(5, 17, 0, 0));
		$this->addPrimarySystem(new CnC(5, 12, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 16, 5, 5));
		$this->addPrimarySystem(new Engine(5, 12, 0, 6, 3));
		$this->addPrimarySystem(new Hangar(4, 4));
		$this->addPrimarySystem(new CargoBay(2, 360));

		$this->addFrontSystem(new Thruster(3, 17, 0, 3, 1));
		$this->addFrontSystem(new PacketTorpedo(3, 6, 5, 300, 60));
		$this->addFrontSystem(new PacketTorpedo(3, 6, 5, 300, 60));
		$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 240, 360));
		$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 0, 120));
		$this->addFrontSystem(new Bulkhead(0, 3));
		$this->addFrontSystem(new Bulkhead(0, 3));

		$this->addAftSystem(new Thruster(3, 21, 0, 6, 2));
		$this->addAftSystem(new StdParticleBeam(2, 4, 1, 180, 300));
		$this->addAftSystem(new StdParticleBeam(2, 4, 1, 60, 180));
		$this->addAftSystem(new Bulkhead(0, 3));
		$this->addAftSystem(new Bulkhead(0, 3));

		$this->addLeftSystem(new Thruster(3, 13, 0, 4, 3));
		$this->addLeftSystem(new TwinArray(3, 6, 2, 120, 360));
		$this->addLeftSystem(new TwinArray(3, 6, 2, 180, 60));
		$this->addLeftSystem(new Hangar(3, 6));
		$this->addLeftSystem(new Bulkhead(0, 3));
		$this->addLeftSystem(new Bulkhead(0, 3));

		$this->addRightSystem(new Thruster(3, 13, 0, 4, 4));
		$this->addRightSystem(new TwinArray(3, 6, 2, 0, 240));
		$this->addRightSystem(new TwinArray(3, 6, 2, 300, 180));
		$this->addRightSystem(new Hangar(3, 6));
		$this->addRightSystem(new Bulkhead(0, 3));
		$this->addRightSystem(new Bulkhead(0, 3));
        
        $this->addFrontSystem(new Structure( 5, 36));
        $this->addAftSystem(new Structure( 3, 36));
        $this->addLeftSystem(new Structure( 3, 36));
        $this->addRightSystem(new Structure( 3, 36));
        $this->addPrimarySystem(new Structure( 5, 40));
		
		
		$this->hitChart = array(
                0=> array(
                        4 => "Structure",
                        12 => "Cargo Bay",
						14 => "Scanner",
                        16 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        5 => "Thruster",
						8 => "Packet Torpedo",
                        10 => "Standard Particle Beam",
						13 => "0:Cargo Bay",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
						8 => "Standard Particle Beam",
						12 => "0:Cargo Bay",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        5 => "Thruster",
						7 => "Twin Array",
						8 => "Hangar",
						10 => "0:Cargo Bay",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        5 => "Thruster",
						7 => "Twin Array",
						8 => "Hangar",
						10 => "0:Cargo Bay",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );

    }
}
?>
