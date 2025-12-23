<?php
class SshelathKastulva extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 500;
	$this->faction = "Escalation Wars Sshel'ath Alliance";
        $this->phpclass = "SshelathKastulva";
        $this->imagePath = "img/ships/EscalationWars/SshelathKasolra.png";
        $this->shipClass = "Kastulva Torpedo Cruiser";
			$this->variantOf = "Kasolra Bombardment Cruiser";
			$this->occurence = "uncommon";		
        $this->shipSizeClass = 3;
		$this->canvasSize = 150; //img has 200px per side
		$this->unofficial = true;

		$this->isd = 1972;
        $this->limited = 33;
	
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 0.75;
        $this->turndelaycost = 1.0;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(4, 15, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 3, 6));
        $this->addPrimarySystem(new Engine(4, 11, 0, 6, 4));
		$this->addPrimarySystem(new Hangar(4, 3));
  
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
		$this->addFrontSystem(new EWEMTorpedo(3, 6, 5, 240, 60));
		$this->addFrontSystem(new EWEMTorpedo(3, 6, 5, 240, 60));
        $this->addFrontSystem(new SubReactorUniversal(3, 8));
		$this->addFrontSystem(new EWEMTorpedo(3, 6, 5, 300, 120));
		$this->addFrontSystem(new EWEMTorpedo(3, 6, 5, 300, 120));

        $this->addAftSystem(new Thruster(3, 22, 0, 6, 2));
		$this->addAftSystem(new EWLightGaussCannon(2, 6, 3, 180, 300));
		$this->addAftSystem(new EWLightGaussCannon(2, 6, 3, 60, 180));

		$this->addLeftSystem(new LightParticleBeamShip(1, 2, 1, 180, 60));
		$this->addLeftSystem(new LightParticleBeamShip(1, 2, 1, 180, 60));
        $this->addLeftSystem(new EWLightGaussCannon(2, 6, 3, 180, 300));
        $this->addLeftSystem(new Thruster(3, 13, 0, 3, 3));

		$this->addRightSystem(new LightParticleBeamShip(1, 2, 1, 300, 180));
		$this->addRightSystem(new LightParticleBeamShip(1, 2, 1, 300, 180));
        $this->addRightSystem(new EWLightGaussCannon(2, 6, 3, 60, 180));
        $this->addRightSystem(new Thruster(3, 13, 0, 3, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(3, 46));
        $this->addAftSystem(new Structure(3, 32));
        $this->addLeftSystem(new Structure(3, 32));
        $this->addRightSystem(new Structure(3, 32));
        $this->addPrimarySystem(new Structure(4, 40));
		
		$this->hitChart = array(
			0=> array(
					10 => "Structure",
					13 => "Scanner",
					15 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					8 => "EM Torpedo",
					9 => "Sub Reactor",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					9 => "Light Gauss Cannon",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					5 => "Thruster",
					8 => "Light Particle Beam",
					10 => "Light Gauss Cannon",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					8 => "Light Particle Beam",
					10 => "Light Gauss Cannon",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
