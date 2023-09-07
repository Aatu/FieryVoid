<?php
class Arcismur extends BaseShip{
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 275;
        $this->faction = "Civilians";
        $this->phpclass = "Arcismur";
        $this->imagePath = "img/ships/arcismus.png";
        $this->shipClass = "Narn Arcismur Heavy Transport";
			$this->variantOf = "Narn Arcismus Supply Ship";
			$this->occurence = "common";
		$this->canvasSize = 170; //img has 200px per side
        $this->shipSizeClass = 3;
	    $this->isCombatUnit = false; //not a combat unit, it will never be present in a regular battlegroup

	    $this->isd = 2212;
        
        $this->forwardDefense = 16;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 5;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
         
        $this->addPrimarySystem(new Reactor(5, 8, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 11, 3, 5));
        $this->addPrimarySystem(new Engine(4, 13, 0, 10, 4));
        $this->addPrimarySystem(new Hangar(4, 4));
		$this->addPrimarySystem(new CargoBay(4, 40));
  
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new LightParticleBeamShip(4, 2, 1, 270, 90));
        $this->addFrontSystem(new LightParticleBeamShip(4, 2, 1, 270, 90));
        
        $this->addRightSystem(new Thruster(4, 15, 0, 3, 4));
        $this->addRightSystem(new CargoBay(4, 30));
        $this->addRightSystem(new LightParticleBeamShip(4, 2, 1, 0, 120));
        
        $this->addLeftSystem(new Thruster(4, 15, 0, 3, 3));
        $this->addLeftSystem(new CargoBay(4, 30));
        $this->addLeftSystem(new LightParticleBeamShip(4, 2, 1, 240, 360));
        
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new LightParticleBeamShip(4, 2, 1, 90, 270));
        $this->addAftSystem(new LightParticleBeamShip(4, 2, 1, 90, 270));
        $this->addAftSystem(new LightParticleBeamShip(4, 2, 1, 90, 270));
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 34));
        $this->addAftSystem(new Structure( 5, 34));
        $this->addLeftSystem(new Structure( 4, 42));
        $this->addRightSystem(new Structure( 4, 42));
        $this->addPrimarySystem(new Structure( 5, 28));       
        
        $this->hitChart = array(
        		0=> array(
        				6 => "Structure",
						11 => "Cargo Bay",
        				13 => "Scanner",
        				15 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				8 => "Thruster",
        				11 => "Light Particle Beam",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				9 => "Light Particle Beam",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				5 => "Thruster",
        				8 => "Light Particle Beam",
        				12 => "Cargo Bay",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				5 => "Thruster",
        				8 => "Light Particle Beam",
        				12 => "Cargo Bay",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
