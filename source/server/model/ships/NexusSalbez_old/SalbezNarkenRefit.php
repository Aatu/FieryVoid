<?php
class SalbezNarkenRefit extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 420;
        $this->faction = "Nexus Sal-bez Coalition (early)";
        $this->phpclass = "SalbezNarkenRefit";
        $this->imagePath = "img/ships/Nexus/salbez_zefjem3.png";
			$this->canvasSize = 115; //img has 200px per side
        $this->shipClass = "Nar-ken Early Scout (2095)";
			$this->variantOf = "Nav-ren Prospector";
			$this->occurence = "uncommon";
			$this->unofficial = true;
        $this->limited = 33;
        $this->isd = 2095;
		
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 30;
         
        $this->addPrimarySystem(new Reactor(3, 12, 0, 0));
        $this->addPrimarySystem(new CnC(3, 9, 0, 0));
        $this->addPrimarySystem(new ELINTScanner(3, 10, 3, 4));
        $this->addPrimarySystem(new Engine(3, 11, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(1, 2));
        $this->addPrimarySystem(new Thruster(3, 14, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 14, 0, 4, 4));
      
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new ELINTScanner(3, 10, 3, 3));
        $this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 270, 90));
        $this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 270, 90));
                
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new LaserCutter(2, 6, 4, 300, 360));
        $this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 120, 300));
        $this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 60, 240));
        $this->addAftSystem(new LaserCutter(2, 6, 4, 0, 60));
 		$this->addAftSystem(new CargoBay(1, 16));
       
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 33));
        $this->addAftSystem(new Structure( 3, 33));
        $this->addPrimarySystem(new Structure( 4, 32));
		
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
                    11 => "Thruster",
                    13 => "ELINT Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    5 => "Thruster",
					8 => "ELINT Scanner",
					10 => "Light Particle Beam",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
                    8 => "Laser Cutter",
					10 => "Light Particle Beam",
					12 => "Cargo Bay",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
