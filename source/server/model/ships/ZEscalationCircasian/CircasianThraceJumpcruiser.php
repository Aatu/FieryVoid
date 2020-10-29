<?php
class CircasianThraceJumpcruiser extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 450;
	$this->faction = "ZEscalation Circasian";
        $this->phpclass = "CircasianThraceJumpcruiser";
        $this->imagePath = "img/ships/EscalationWars/CircasianThraceJumpcruiser.png";
        $this->shipClass = "Thrace Jump Cruiser";
        $this->shipSizeClass = 3;
		$this->canvasSize = 175; //img has 200px per side
		$this->unofficial = true;
		
		$this->fighters = array("normal"=>6);


	$this->isd = 1975;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(5, 20, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 4, 6));
        $this->addPrimarySystem(new Engine(5, 15, 0, 12, 3));
		$this->addPrimarySystem(new Hangar(4, 8));
   
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));


        $this->addAftSystem(new Thruster(3, 16, 0, 6, 2));
        $this->addAftSystem(new Thruster(3, 16, 0, 6, 2));
        $this->addAftSystem(new JumpEngine(3, 11, 3, 36));
        $this->addAftSystem(new LightParticleCannon(2, 6, 5, 180, 240));
        $this->addAftSystem(new LightParticleCannon(2, 6, 5, 120, 180));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 120, 300));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 60, 240));
		

		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        $this->addLeftSystem(new LightParticleCannon(3, 6, 5, 300, 360));
        $this->addLeftSystem(new Thruster(3, 15, 0, 4, 3));

		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        $this->addRightSystem(new LightParticleCannon(3, 6, 5, 0, 60));
        $this->addRightSystem(new Thruster(3, 15, 0, 4, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 40));
        $this->addAftSystem(new Structure(4, 40));
        $this->addLeftSystem(new Structure(4, 44));
        $this->addRightSystem(new Structure(4, 44));
        $this->addPrimarySystem(new Structure(4, 45));
		
		$this->hitChart = array(
			0=> array(
					10 => "Structure",
					12 => "Scanner",
					14 => "Engine",
					16 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					3 => "Thruster",
					6 => "Medium Plasma Cannon",
					8 => "Light Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					5 => "Thruster",
					7 => "Light Particle Cannon",
					9 => "Light Particle Beam",
					11 => "Jump Engine",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					6 => "Light Particle Cannon",
					9 => "Light Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					6 => "Light Particle Cannon",
					9 => "Light Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
