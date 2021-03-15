<?php
class PorcupineAlpha extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 250;
	$this->faction = 'EA';//"EA defenses";
        $this->phpclass = "PorcupineAlpha";
        $this->imagePath = "img/ships/porcupine.png";
        $this->shipClass = "Porcupine Light Carrier (Alpha)";
//			$this->variantOf = "Shepherd Fighter Transport (Alpha)";
//			$this->occurence = "common";
        $this->canvasSize = 80;
 		$this->unofficial = true;

		$this->fighters = array("normal"=>12); 
        
        $this->isd = 2180;

        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
		$this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(3, 10, 0, 0));
        $this->addPrimarySystem(new CnC(4, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 3, 5));
        $this->addPrimarySystem(new Engine(3, 9, 0, 6, 2));
		$this->addPrimarySystem(new Hangar(3, 2));
		$this->addPrimarySystem(new Thruster(3, 11, 0, 3, 3));
		$this->addPrimarySystem(new Thruster(3, 11, 0, 3, 4));
		
        $this->addFrontSystem(new Thruster(3, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 6, 0, 2, 1));
		$this->addFrontSystem(new Hangar(1, 12));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 240, 60));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 300, 120));
		$this->addFrontSystem(new InterceptorMkI(2, 4, 1, 270, 90));
		
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 180, 360));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addAftSystem(new InterceptorMkI(2, 4, 1, 90, 270));
	
        $this->addPrimarySystem(new Structure( 4, 48));

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
                    5 => "Thruster",
					7 => "Standard Particle Beam",
                    8 => "Interceptor I",
					11 => "Hangar",
					17 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
					8 => "Standard Particle Beam",
                    9 => "Interceptor I",
                    17 => "Structure",
                    20 => "Primary",
            ),
        );		
    }

}



?>
