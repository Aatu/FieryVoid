<?php
class mentabanAraskaStrikeCarrier extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 600;
	$this->faction = "Small Races";
        $this->phpclass = "mentabanAraskaStrikeCarrier";
        $this->imagePath = "img/ships/mentabanAraska.png";
        $this->shipClass = "Mentaban Araska Strike Carrier";
        $this->shipSizeClass = 3;
        $this->limited = 33;	    
		$this->isd = 2250;
		$this->unofficial = true;
		
        $this->fighters = array("fighters" => 18);

        $this->forwardDefense = 13;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 0;
		
		$this->notes = "Only 2 exist.";


        $this->addPrimarySystem(new Reactor(4, 20, 0, 0));
        $this->addPrimarySystem(new CnC(4, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 3, 6));
        $this->addPrimarySystem(new Engine(4, 16, 0, 8, 3));
        $this->addPrimarySystem(new JumpEngine(4, 20, 4, 24));
		$this->addPrimarySystem(new Hangar(4, 16, 6));
		$this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 360)); 


		$this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
		$this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
		$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 300, 60));
		$this->addFrontSystem(new Hangar(4, 6, 6));
		$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 300, 60));
		$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 240, 60));
		$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 300, 120));

		$this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
		$this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
		$this->addAftSystem(new StdParticleBeam(2, 4, 1, 120, 240));
		$this->addAftSystem(new StdParticleBeam(2, 4, 1, 120, 300));
		$this->addAftSystem(new StdParticleBeam(2, 4, 1, 60, 240));
		$this->addAftSystem(new StdParticleBeam(2, 4, 1, 120, 240));

		$this->addLeftSystem(new Thruster(3, 15, 0, 5, 3));
		$this->addLeftSystem(new Railgun(5, 9, 6, 300, 360));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 360));

		$this->addRightSystem(new Thruster(3, 15, 0, 5, 4));
		$this->addRightSystem(new Railgun(5, 9, 6, 0, 60));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 42));
        $this->addAftSystem(new Structure( 4, 42));
        $this->addLeftSystem(new Structure( 4, 55));
        $this->addRightSystem(new Structure( 4, 55));
        $this->addPrimarySystem(new Structure( 5, 40));
		
	
		$this->hitChart = array(
                0=> array(
                        10 => "Structure",
                        11 => "Jump Engine",
                        12 => "Standard Particle Beam",
                        16 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        6 => "Standard Particle Beam",
                        8 => "Hangar",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        8 => "Standard Particle Beam",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
                        6 => "Medium Railgun",
                        8 => "Standard Particle Beam",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
                        6 => "Medium Railgun",
                        8 => "Standard Particle Beam",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );
    }
}
