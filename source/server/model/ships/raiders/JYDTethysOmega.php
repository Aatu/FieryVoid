<?php
class JYDTethysOmega extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 485;
		$this->faction = "Raiders";
        $this->phpclass = "JYDTethysOmega";
        $this->imagePath = "img/ships/tethys.png";
        $this->shipClass = "JYD Tethys (Omega)";
//		        $this->occurence = "unique";
        $this->canvasSize = 100;
        $this->fighters = array("light"=>6);
	    
		$this->notes = 'Used only by the Junkyard Dogs';
		$this->notes .= '<br>Only two exist';
	    $this->isd = 2260;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 13;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 1;
		$this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(4, 17, 0, -1));
        $this->addPrimarySystem(new CnC(4, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 14, 4, 7));
        $this->addPrimarySystem(new Engine(4, 11, 0, 8, 2));
		$this->addPrimarySystem(new Hangar(4, 2));
		$this->addPrimarySystem(new Thruster(3, 15, 0, 4, 3));
		$this->addPrimarySystem(new Thruster(3, 15, 0, 4, 4));
		
        $this->addFrontSystem(new Thruster(3, 9, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 9, 0, 3, 1));
        $this->addFrontSystem(new MediumPulse(3, 6, 3, 270, 90));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 180, 360));
        $this->addFrontSystem(new LightParticleCannon(3, 6, 5, 270, 90));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 0, 180));
        $this->addFrontSystem(new MediumPulse(3, 6, 3, 270, 90));
		
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 180, 360));
        $this->addAftSystem(new MediumPulse(3, 6, 3, 90, 270));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 0, 180));
	
        $this->addPrimarySystem(new Structure( 4, 44));
        
		$this->hitChart = array(
                0=> array(
                        8 => "Thruster",
                        11 => "Scanner",
                        14 => "Engine",
                        16 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        6 => "Thruster",
                        8 => "Medium Pulse Cannon",
                        10 => "Light Particle Cannon",
                        12 => "Standard Particle Beam",
                        17 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        8 => "Thruster",
                        10 => "Standard Particle Beam",
                        12 => "Medium Pulse Cannon",
                        17 => "Structure",
                        20 => "Primary",
                ),
        );

    }

}



?>
