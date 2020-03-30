<?php
class Athasa extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 600;
        $this->faction = "Balosian";
        $this->phpclass = "Athasa";
        $this->imagePath = "img/ships/esthasa.png";
        $this->shipClass = "Athasa Scout";
        $this->variantOf = "Esthasa Destroyer";
        $this->occurence = "rare";
        $this->fighters = array("medium"=>6);
		$this->isd = 2218;
                
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.50;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 30;
         
        $this->addPrimarySystem(new Reactor(6, 17, 0, 0));
        $this->addPrimarySystem(new CnC(6, 12, 0, 0));
        $this->addPrimarySystem(new ElintScanner(5, 18, 5, 8));
        $this->addPrimarySystem(new Engine(5, 15, 0, 10, 3));
        $this->addPrimarySystem(new Hangar(5, 7));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 4));
        
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new StdParticleBeam(3, 4, 1, 180, 60));
        $this->addFrontSystem(new StdParticleBeam(3, 4, 1, 270, 90));
        $this->addFrontSystem(new StdParticleBeam(3, 4, 1, 300, 180));
        $this->addFrontSystem(new IonCannon(4, 6, 4, 240, 0));
        $this->addFrontSystem(new IonCannon(4, 6, 4, 0, 120));
        
        $this->addAftSystem(new JumpEngine(4, 16, 4, 36));
        $this->addAftSystem(new Thruster(4, 14, 0, 5, 2));
        $this->addAftSystem(new Thruster(4, 14, 0, 5, 2));
        $this->addAftSystem(new StdParticleBeam(3, 4, 1, 120, 0));
        $this->addAftSystem(new StdParticleBeam(3, 4, 1, 0, 240));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 60));
        $this->addAftSystem(new Structure( 4, 60));
        $this->addPrimarySystem(new Structure( 6, 46 ));
		
		$this->hitChart = array(
            0=> array(
                    6 => "Structure",
					9 => "Thruster",
					12 => "Scanner",
                    15 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
                    6 => "Ion Cannon",
                    9 => "Standard Particle Beam",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    4 => "Thruster",
					6 => "Jump Engine",
					8 => "Standard Particle Beam",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
