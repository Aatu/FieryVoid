<?php
class ShoKar extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 575;
		$this->faction = "Narn";
        $this->phpclass = "ShoKar";
        $this->imagePath = "img/ships/shokar.png";
        $this->shipClass = "Sho'Kar Light Scout Cruiser";
        $this->limited = 33;
        $this->fighters = array("normal"=>6);
	    $this->isd = 2240;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        
        $this->iniativebonus = 10;
        
        $this->addPrimarySystem(new Reactor(5, 17, 0, 3));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new ElintScanner(5, 21, 5, 10));
        //$this->addPrimarySystem(new ElintArray(6, 6, 2));
        $this->addPrimarySystem(new Engine(5, 16, 0, 10, 2));
		$this->addPrimarySystem(new JumpEngine(5, 24, 3, 20));
		$this->addPrimarySystem(new Hangar(5, 8));
        
        $this->addFrontSystem(new LightPulse(2, 4, 2, 270, 90));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 270, 90));
        $this->addFrontSystem(new LightPulse(2, 4, 2, 270, 90));        
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
		
        $this->addAftSystem(new LightPulse(2, 4, 2, 90, 270));
        $this->addAftSystem(new TwinArray(3, 6, 2, 120, 300));
        $this->addAftSystem(new TwinArray(3, 6, 2, 60, 240));
        $this->addAftSystem(new LightPulse(2, 4, 2, 90, 270));        
        $this->addAftSystem(new Thruster(3, 12, 0, 5, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 5, 2));
		
		$this->addLeftSystem(new BurstBeam(3, 6, 3, 240, 0));
		$this->addLeftSystem(new Thruster(3, 15, 0, 4, 3));
		
		$this->addRightSystem(new BurstBeam(3, 6, 3, 0, 120));
		$this->addRightSystem(new Thruster(3, 15, 0, 4, 4));
		
		//structures
        $this->addFrontSystem(new Structure(4, 42));
        $this->addAftSystem(new Structure(3, 38));
        $this->addLeftSystem(new Structure(4, 42));
        $this->addRightSystem(new Structure(4, 42));
        $this->addPrimarySystem(new Structure(5, 36));        
		
		
		$this->hitChart = array(
			0=> array(
				8 => "Structure",
				11 => "Jump Engine",
				13 => "ELINT Scanner",
				15 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				6 => "Thruster",
				8 => "Twin Array",
				11 => "Light Pulse Cannon",
				18 => "Structure",
				20 => "primary",
			),
			2=> array(
				6 => "Thruster",
				8 => "Light Pulse Cannon",
				11 => "Twin Array",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				6 => "Thruster",
				9 => "Burst Beam",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				6 => "Thruster",
				9 => "Burst Beam",
				18 => "Structure",
				20 => "Primary",
			),
		);
    }

}

