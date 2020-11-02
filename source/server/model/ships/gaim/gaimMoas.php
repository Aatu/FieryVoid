<?php
class GaimMoas extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 625;
	$this->faction = "Custom Ships";
        $this->phpclass = "GaimMoas";
        $this->imagePath = "img/ships/MarkabCruiser.png";
        $this->shipClass = "Moas Gunship";
        $this->shipSizeClass = 3;
	    $this->isd = 2254;
		
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 4;

        
        $this->addPrimarySystem(new Reactor(4, 17, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 18, 8, 8));
        $this->addPrimarySystem(new Engine(4, 12, 0, 8, 2));
		$this->addPrimarySystem(new Hangar(4, 4));
        
        $this->addFrontSystem(new ParticleConcentrator(4, 0, 0, 300, 60));
        $this->addFrontSystem(new HeavyPulse(4, 6, 4, 300, 60));
        $this->addFrontSystem(new HeavyPulse(4, 6, 4, 300, 60));
        $this->addFrontSystem(new Bulkhead(0, 2));
        $this->addFrontSystem(new Bulkhead(0, 2));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));   
			
		
        $this->addAftSystem(new Bulkhead(0, 2));
        $this->addAftSystem(new Bulkhead(0, 2));
        $this->addAftSystem(new ScatterGun(2, 0, 0, 120, 0));
        $this->addAftSystem(new ScatterGun(2, 0, 0, 0, 240));
        $this->addAftSystem(new Thruster(4, 13, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 13, 0, 4, 2));
		
        $this->addLeftSystem(new PacketTorpedo(4, 0, 0, 240, 360));
        $this->addLeftSystem(new ScatterGun(2, 0, 0, 180, 60));
        $this->addLeftSystem(new Bulkhead(0, 4));
        $this->addLeftSystem(new Thruster(4, 13, 0, 3, 3));
        
        $this->addRightSystem(new PacketTorpedo(4, 0, 0, 0, 120));
        $this->addRightSystem(new ScatterGun(2, 0, 0, 300, 180));
        $this->addRightSystem(new Bulkhead(0, 4));
        $this->addRightSystem(new Thruster(4, 13, 0, 3, 4));
              			          
		
		//structures
        $this->addFrontSystem(new Structure(4, 36));
        $this->addAftSystem(new Structure(4, 36));
        $this->addLeftSystem(new Structure(4, 52));
        $this->addRightSystem(new Structure(4, 52));
        $this->addPrimarySystem(new Structure(4, 36));
		
		
		$this->hitChart = array(
			0=> array(
				9 => "Structure",
				12 => "Scanner",
				15 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				3 => "Thruster",
				5 => "Particle Concentrator",
				8 => "Heavy Pulse Cannon",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				6 => "Thruster",
				9 => "Scattergun",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				4 => "Thruster",
				6 => "Packet Torpedo",
				8 => "Scattergun",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				4 => "Thruster",
				6 => "Packet Torpedo",
				8 => "Scattergun",
				18 => "Structure",
				20 => "Primary",
			),
		);        
    }	
}



?>
