<?php
class JYDShokos extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 325;
		$this->faction = "Raiders";
        $this->phpclass = "JYDShokos";
        $this->imagePath = "img/ships/shokos.png";
        $this->shipClass = "JYD Sho'Kos Patrol Cutter";
        $this->agile = true;
        $this->canvasSize = 100;
	    $this->isd = 2260;
        
		$this->notes = 'Used only by the Junkyard Dogs';
		$this->notes .= '<br>Only two exist';

        $this->forwardDefense = 10;
        $this->sideDefense = 12;
        
        $this->turncost = 0.50;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
		$this->iniativebonus = 60;
        
         
        $this->addPrimarySystem(new Reactor(3, 12, 0, 2));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 14, 3, 6));
        $this->addPrimarySystem(new Engine(3, 10, 0, 12, 3));
		$this->addPrimarySystem(new Hangar(3, 1));
		$this->addPrimarySystem(new Thruster(3, 10, 0, 5, 3));
		$this->addPrimarySystem(new Thruster(3, 10, 0, 5, 4));
		
				
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new StdParticleBeam(3, 4, 1, 240, 60));
        $this->addFrontSystem(new StdParticleBeam(3, 4, 1, 300, 120));
		$this->addFrontSystem(new mediumPulse(3, 6, 3, 240, 120));		
		
        $this->addAftSystem(new Thruster(3, 10, 0, 6, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 6, 2));
		$this->addAftSystem(new LightPulse(2, 4, 2, 180, 60));
        $this->addAftSystem(new LightPulse(2, 4, 2, 300, 180));
        
       
        $this->addPrimarySystem(new Structure( 3, 48));
				
	
		$this->hitChart = array(
			0=> array(
				7 => "Thruster",
				11 => "Scanner",
				14 => "Engine",
				16 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				6 => "Thruster",
				8 => "Medium Pulse Cannon",
				12 => "Standard Particle Beam",
				17 => "Structure",
				20 => "Primary",
			),
			2=> array(
				6 => "Thruster",
				10 => "Light Pulse Cannon",
				17 => "Structure",
				20 => "Primary",
			),
		);				
    }
}



?>
