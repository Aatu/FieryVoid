<?php
class CircasianMishaJumpCarrier extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 480;
	$this->faction = "ZEscalation Circasian Empire";
        $this->phpclass = "CircasianMishaJumpCarrier";
        $this->imagePath = "img/ships/EscalationWars/CircasianMisha.png";
        $this->shipClass = "Misha Jump Carrier";
        $this->shipSizeClass = 3;
		$this->canvasSize = 175; //img has 200px per side
		$this->unofficial = true;
		
		$this->fighters = array("medium"=>24);


	$this->isd = 1978;
        
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
        $this->addPrimarySystem(new Scanner(4, 16, 4, 7));
        $this->addPrimarySystem(new Engine(5, 15, 0, 12, 3));
		$this->addPrimarySystem(new Hangar(5, 27));
        $this->addPrimarySystem(new JumpEngine(4, 15, 3, 32));
		
   
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));


        $this->addAftSystem(new Thruster(3, 15, 0, 6, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 6, 2));
        $this->addAftSystem(new MediumPlasma(3, 5, 3, 120, 240));
        $this->addAftSystem(new MediumPlasma(3, 5, 3, 120, 240));
		
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
					12 => "Scanner",
					14 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					8 => "Medium Plasma Cannon",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Medium Plasma Cannon",
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
