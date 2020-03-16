<?php
class Bintak extends BaseShip{
    
    function __construct($id, $userid, $name, $movement){
        parent::__construct($id, $userid, $name, $movement);
        
		$this->pointCost = 1250;
		$this->faction = "Narn";
        $this->phpclass = "Bintak";
        $this->imagePath = "img/ships/bintak.png";
        $this->shipClass = "Bin'Tak Dreadnought";
        $this->shipSizeClass = 3;
        $this->canvasSize = 280;
        $this->limited = 10;
        $this->fighters = array("normal"=>18);
	    $this->isd = 2245;
		
        $this->forwardDefense = 16;
        $this->sideDefense = 18;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 4;

        
        $this->addPrimarySystem(new Reactor(7, 30, 0, 0));
        $this->addPrimarySystem(new CnC(7, 28, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 32, 6, 10));
        $this->addPrimarySystem(new Engine(6, 28, 0, 12, 3));
		$this->addPrimarySystem(new JumpEngine(6, 30, 5, 20));
		$this->addPrimarySystem(new Hangar(6, 22));
        
        $this->addFrontSystem(new LightPulse(3, 4, 2, 300, 60));
        $this->addFrontSystem(new MagGun(5, 9, 8, 300, 60));
        
        $this->addFrontSystem(new HeavyLaser(5, 8, 6, 300, 60));
        
        $this->addFrontSystem(new Thruster(5, 15, 0, 5, 1));
        $this->addFrontSystem(new Thruster(5, 15, 0, 5, 1));
        
        $this->addFrontSystem(new HeavyLaser(5, 8, 6, 300, 60));
        
		$this->addFrontSystem(new EnergyMine(5, 5, 4, 300, 60));
		$this->addFrontSystem(new EnergyMine(5, 5, 4, 300, 60));
		//aft
		          

		$this->addAftSystem(new HeavyLaser(5, 8, 6, 180, 300));
		$this->addAftSystem(new HeavyLaser(5, 8, 6, 60, 180));
		$this->addAftSystem(new LightPulse(3, 4, 2, 90, 270));
		$this->addAftSystem(new LightPulse(3, 4, 2, 90, 270));
		$this->addAftSystem(new TwinArray(3, 6, 2, 90, 270));
		
        $this->addAftSystem(new Thruster(5, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(5, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(5, 12, 0, 3, 2));
		$this->addAftSystem(new Thruster(5, 12, 0, 3, 2));
        
		//left
		
		$this->addLeftSystem(new HeavyLaser(5, 8, 6, 240, 0));
		$this->addLeftSystem(new LightPulse(3, 4, 2, 240, 60));
		$this->addLeftSystem(new TwinArray(3, 6, 2, 240, 0));
		$this->addLeftSystem(new TwinArray(3, 6, 2, 240, 0));
		$this->addLeftSystem(new IonTorpedo(5, 5, 4, 240, 0));
		$this->addLeftSystem(new Thruster(5, 20, 0, 6, 3));
              

		//right
		$this->addRightSystem(new HeavyLaser(5, 8, 6, 0, 120));
		$this->addRightSystem(new LightPulse(3, 4, 2, 300, 120));
		$this->addRightSystem(new TwinArray(3, 6, 2, 0, 120));
		$this->addRightSystem(new TwinArray(3, 6, 2, 0, 120));
		$this->addRightSystem(new IonTorpedo(5, 5, 4, 0, 120));
		$this->addRightSystem(new Thruster(5, 20, 0, 6, 4));
        
		
		//structures
        $this->addFrontSystem(new Structure(6, 108));
        $this->addAftSystem(new Structure(5, 87));
        $this->addLeftSystem(new Structure(5, 96));
        $this->addRightSystem(new Structure(5, 96));
        $this->addPrimarySystem(new Structure(6, 72));
     
		$this->hitChart = array(
			0=> array(
				8 => "Structure",
				11 => "Jump Engine",
				13 => "Scanner",
				15 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				3 => "Thruster",
				5 => "Heavy Laser",
				6 => "Mag Gun",
				8 => "Energy Mine",
				9 => "Light Pulse Cannon",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				6 => "Thruster",
				8 => "Heavy Laser",
				9 => "Twin Array",
				11 => "Light Pulse Cannon",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				3 => "Thruster",
				5 => "Heavy Laser",
				6 => "Light Pulse Cannon",
				8 => "Twin Array",
				9 => "Ion torpedo",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				3 => "Thruster",
				5 => "Heavy Laser",
				6 => "Light Pulse Cannon",
				8 => "Twin Array",
				9 => "Ion torpedo",
				18 => "Structure",
				20 => "Primary",
			),
		);
	 
    }
}



?>
