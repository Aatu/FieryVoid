<?php
class CircasianAscharTroopTransport extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 400;
	$this->faction = "ZEscalation Circasian";
        $this->phpclass = "CircasianAscharTroopTransport";
        $this->imagePath = "img/ships/EscalationWars/CircasianNalessinFreighter.png";
        $this->shipClass = "Aschar Troop Transport";
			$this->variantOf = "Nalessin Military Freighter";
			$this->occurence = "common";		
        $this->shipSizeClass = 3;
		$this->canvasSize = 175; //img has 200px per side
		$this->unofficial = true;


	$this->isd = 1970;

		$this->fighters = array("normal"=>6, "assault shuttles"=>12);
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(4, 15, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 4, 6));
        $this->addPrimarySystem(new Engine(4, 15, 0, 10, 4));
		$this->addPrimarySystem(new Hangar(4, 20));
   
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));


        $this->addAftSystem(new Thruster(2, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 4, 0, 1, 2));
        $this->addAftSystem(new Thruster(2, 4, 0, 1, 2));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 180, 300));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 60, 180));


        $this->addLeftSystem(new LightParticleCannon(3, 6, 5, 240, 360));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        $this->addLeftSystem(new Thruster(3, 13, 0, 5, 3));
		$this->addLeftSystem(new CargoBay(2, 26));


        $this->addRightSystem(new LightParticleCannon(3, 6, 5, 0, 120));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        $this->addRightSystem(new Thruster(3, 13, 0, 5, 4));
		$this->addRightSystem(new CargoBay(2, 26));

        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(3, 36));
        $this->addAftSystem(new Structure(3, 28));
        $this->addLeftSystem(new Structure(3, 40));
        $this->addRightSystem(new Structure(3, 40));
        $this->addPrimarySystem(new Structure(4, 31));
		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					10 => "Scanner",
					12 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					5 => "Thruster",
					7 => "Light Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					7 => "Thruster",
					9 => "Light Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					6 => "Light Particle Cannon",
					7 => "Light Particle Beam",
					9 => "Cargo Bay",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					6 => "Light Particle Cannon",
					7 => "Light Particle Beam",
					9 => "Cargo Bay",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
