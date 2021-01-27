<?php
class CircasianSularaScoutCarrier extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 575;
	$this->faction = "ZEscalation Circasian";
        $this->phpclass = "CircasianSularaScoutCarrier";
        $this->imagePath = "img/ships/EscalationWars/CircasianMisha.png";
        $this->shipClass = "Sulara Scout Carrier";
			$this->variantOf = "Misha Jump Carrier";
			$this->occurence = "rare";		
        $this->shipSizeClass = 3;
		$this->canvasSize = 175; //img has 200px per side
		$this->unofficial = true;
		
		$this->fighters = array("medium"=>18);


	$this->isd = 1986;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(5, 18, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new ELINTScanner(4, 20, 4, 8));
        $this->addPrimarySystem(new Engine(5, 15, 0, 12, 3));
		$this->addPrimarySystem(new Hangar(5, 20));
        $this->addPrimarySystem(new JumpEngine(4, 15, 3, 32));
		
   
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new EWGatlingParticleBeam(2, 4, 3, 240, 60));
        $this->addFrontSystem(new EWGatlingParticleBeam(2, 4, 3, 300, 120));
        $this->addFrontSystem(new EWParticleMaul(3, 8, 3, 300, 60));
        $this->addFrontSystem(new EWParticleMaul(3, 8, 3, 300, 60));

        $this->addAftSystem(new Thruster(3, 15, 0, 6, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 6, 2));
        $this->addAftSystem(new EWParticleMaul(3, 8, 3, 120, 240));
        $this->addAftSystem(new EWParticleMaul(3, 8, 3, 120, 240));
		
		$this->addLeftSystem(new EWGatlingParticleBeam(2, 4, 3, 180, 360));
		$this->addLeftSystem(new EWGatlingParticleBeam(2, 4, 3, 180, 360));
        $this->addLeftSystem(new Thruster(3, 15, 0, 4, 3));

		$this->addRightSystem(new EWGatlingParticleBeam(2, 4, 3, 0, 180));
		$this->addRightSystem(new EWGatlingParticleBeam(2, 4, 3, 0, 180));
        $this->addRightSystem(new Thruster(3, 15, 0, 4, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 42));
        $this->addAftSystem(new Structure(4, 42));
        $this->addLeftSystem(new Structure(4, 48));
        $this->addRightSystem(new Structure(4, 48));
        $this->addPrimarySystem(new Structure(4, 42));
		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					10 => "Jump Engine",
					12 => "ELINT Scanner",
					14 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					3 => "Thruster",
					6 => "Gatling Particle Beam",
					9 => "Particle Maul",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Particle Maul",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					5 => "Thruster",
					8 => "Gatling Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					8 => "Gatling Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
