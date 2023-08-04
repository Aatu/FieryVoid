<?php
class Arcismus extends BaseShip{
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 375;
        $this->faction = "Civilians";
        $this->phpclass = "Arcismus";
        $this->imagePath = "img/ships/arcismus.png";
        $this->shipClass = "Narn Arcismus Supply Ship";
		$this->canvasSize = 170; //img has 200px per side
        $this->shipSizeClass = 3;
	    $this->isCombatUnit = false; //not a combat unit, it will never be present in a regular battlegroup

	    $this->isd = 2242;
        
        $this->forwardDefense = 16;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 5;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
         
        $this->addPrimarySystem(new Reactor(5, 13, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 11, 3, 6));
        $this->addPrimarySystem(new Engine(4, 13, 0, 10, 4));
        $this->addPrimarySystem(new Hangar(4, 4));
		$this->addPrimarySystem(new JumpEngine(4, 15, 3, 32));
		$this->addPrimarySystem(new CargoBay(4, 20));
  
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new BurstBeam(4, 6, 3, 270, 90));
        $this->addFrontSystem(new BurstBeam(4, 6, 3, 270, 90));
        
        $this->addRightSystem(new Thruster(4, 15, 0, 3, 4));
        $this->addRightSystem(new CargoBay(4, 30));
        $this->addRightSystem(new LightPulse(2, 4, 2, 0, 120));
        
        $this->addLeftSystem(new Thruster(4, 15, 0, 3, 3));
        $this->addLeftSystem(new CargoBay(4, 30));
        $this->addLeftSystem(new LightPulse(2, 4, 2, 240, 360));
        
        $this->addAftSystem(new Thruster(3, 12, 0, 5, 2));
        $this->addAftSystem(new LightPulse(4, 4, 2, 90, 270));
        $this->addAftSystem(new BurstBeam(2, 6, 3, 90, 270));
        $this->addAftSystem(new LightPulse(4, 4, 2, 90, 270));
        $this->addAftSystem(new Thruster(3, 12, 0, 5, 2));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 34));
        $this->addAftSystem(new Structure( 5, 34));
        $this->addLeftSystem(new Structure( 4, 42));
        $this->addRightSystem(new Structure( 4, 42));
        $this->addPrimarySystem(new Structure( 5, 28));       
        
        $this->hitChart = array(
        		0=> array(
        				6 => "Structure",
						8 => "Cargo Bay",
						11 => "Jump Engine",
        				13 => "Scanner",
        				15 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				8 => "Thruster",
        				11 => "Burst Beam",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				9 => "Light Pulse Cannon",
						11 => "Burst Beam",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				5 => "Thruster",
        				8 => "Light Pulse Cannon",
        				12 => "Cargo Bay",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				5 => "Thruster",
        				8 => "Light Pulse Cannon",
        				12 => "Cargo Bay",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
