<?php
class TethysBeta extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 300;
		$this->faction = "EA (early)";
        $this->phpclass = "TethysBeta";
        $this->imagePath = "img/ships/tethys.png";
        $this->shipClass = "Tethys Police Cutter (Beta)";
			$this->variantOf = "Tethys Police Cutter (Alpha)";
			$this->occurence = "common";
        $this->canvasSize = 100;
	    
	    $this->isd = 2158;
 		$this->unofficial = true;
       
        $this->forwardDefense = 13;
        $this->sideDefense = 13;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 1;
		$this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(4, 9, 0, 0));
        $this->addPrimarySystem(new CnC(4, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 2, 4));
        $this->addPrimarySystem(new Engine(4, 11, 0, 6, 2));
		$this->addPrimarySystem(new Hangar(4, 2));
		$this->addPrimarySystem(new Thruster(3, 7, 0, 3, 3));
		$this->addPrimarySystem(new Thruster(3, 7, 0, 3, 4));
		
        $this->addFrontSystem(new Thruster(3, 7, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 7, 0, 3, 1));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 240, 360));
        $this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 270, 90));
        $this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 270, 90));
        $this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 270, 90));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 0, 120));
		
        $this->addAftSystem(new Thruster(4, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 3, 2));
        $this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 180, 360));
        $this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 90, 270));
        $this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 0, 180));
	
        $this->addPrimarySystem(new Structure( 4, 38));
        
		$this->hitChart = array(
                0=> array(
                        9 => "Thruster",
                        11 => "Scanner",
                        14 => "Engine",
                        16 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        6 => "Thruster",
                        8 => "Medium Plasma Cannon",
                        12 => "Light Particle Beam",
                        17 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        8 => "Thruster",
                        10 => "Light Particle Beam",
                        17 => "Structure",
                        20 => "Primary",
                ),
        );

    }

}



?>
