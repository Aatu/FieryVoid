<?php
class gaimRakar extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 750;
        $this->faction = "Gaim";
        $this->phpclass = "gaimRakar";
        $this->imagePath = "img/ships/GaimRakar.png";
		$this->canvasSize = 200;
        $this->shipClass = "Rakar Strike Cruiser";
        $this->shipSizeClass = 3;
        $this->fighters = array("normal"=>18);
	    $this->isd = 2253;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
         
        $this->addPrimarySystem(new Reactor(5, 25, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 4, 6));
        $this->addPrimarySystem(new Engine(5, 20, 0, 12, 2));
        $this->addPrimarySystem(new Hangar(5, 20));
        $this->addPrimarySystem(new JumpEngine(5, 15, 4, 24));
  
        $this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));
		$this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));
        $this->addFrontSystem(new HeavyPulse(4, 6, 4, 300, 60));
        $this->addFrontSystem(new TwinArray(2, 6, 2, 240, 60));
        $this->addFrontSystem(new PacketTorpedo(4, 6, 5, 300, 60));
		$this->addFrontSystem(new PacketTorpedo(4, 6, 5, 300, 60));
        $this->addFrontSystem(new TwinArray(2, 6, 2, 300, 120));
        $this->addFrontSystem(new HeavyPulse(4, 6, 4, 300, 60));
		$this->addFrontSystem(new Bulkhead(0, 3));
		$this->addFrontSystem(new Bulkhead(0, 3));
	    
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 120, 300));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 60, 240));
        $this->addAftSystem(new Thruster(3, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 16, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 16, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 2, 2));
		$this->addAftSystem(new Bulkhead(0, 3));
                
        $this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));
        $this->addLeftSystem(new ScatterGun(3, 8, 3, 180, 360));
        $this->addLeftSystem(new TwinArray(2, 6, 2, 180, 360));
		$this->addLeftSystem(new Bulkhead(0, 3));
  
        $this->addRightSystem(new Thruster(4, 15, 0, 5, 4));
        $this->addRightSystem(new ScatterGun(3, 8, 3, 0, 180));
        $this->addRightSystem(new TwinArray(2, 6, 2, 0, 180));
		$this->addRightSystem(new Bulkhead(0, 3));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 52));
        $this->addAftSystem(new Structure( 4, 52));
        $this->addLeftSystem(new Structure( 5, 52));
        $this->addRightSystem(new Structure( 5, 52));
        $this->addPrimarySystem(new Structure( 5, 55));
        
        $this->hitChart = array(
        		0=> array(
        				7 => "Structure",
        				9 => "Jump Engine",
        				12 => "Scanner",
        				14 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				4 => "Thruster",
        				6 => "Heavy Pulse Cannon",
        				10 => "Packet Torpedo",
        				12 => "Twin Array",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				8 => "Thruster",
        				10 => "Standard Particle Beam",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				4 => "Thruster",
        				5 => "Twin Array",
					7 => "Scattergun",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				4 => "Thruster",
        				5 => "Twin Array",
					7 => "Scattergun",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
