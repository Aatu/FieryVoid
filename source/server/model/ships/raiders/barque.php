<?php
class Barque extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 525;
        $this->faction = "Raiders";
        $this->phpclass = "Barque";
        $this->imagePath = "img/ships/DenethProtector.png"; //need to change
        $this->shipClass = "Barque";
        $this->occurence = "common";
		$this->canvasSize = 100; //img has 140px per side
        $this->fighters = array("normal"=>6);
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;
        
        $this->addPrimarySystem(new Reactor(4, 21, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 4, 6));
        $this->addPrimarySystem(new Engine(4, 16, 0, 9, 2));
        $this->addPrimarySystem(new CargoBay(3, 6));
        $this->addPrimarySystem(new CargoBay(3, 6));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 4, 4));
		$this->addPrimarySystem(new Hangar(3, 8));
        
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
		$this->addFrontSystem(new MediumPulse(3, 6, 3, 240, 0));
		$this->addFrontSystem(new MediumPulse(3, 6, 3, 0, 120));
		$this->addFrontSystem(new TwinArray(2, 6, 2, 240, 60));
		$this->addFrontSystem(new TwinArray(2, 6, 2, 300, 120));
		$this->addFrontSystem(new HeavyPlasma(3, 8, 5, 300, 60));		
        
        $this->addAftSystem(new Thruster(3, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 3, 2));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 180, 300));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 60, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 50));
        $this->addPrimarySystem(new Structure( 4, 50));
        $this->addAftSystem(new Structure( 4, 42));
        
        $this->hitChart = array(
        	0=> array(
        		7 => "Structure",
        		9 => "Cargo Bay",
        		12 => "Thruster",
        		14 => "Scanner",
        		16 => "Engine",
        		17 => "Hangar",
        		19 => "Reactor",
        		20 => "C&C",	
        	),
        	1=> array(
        		4 => "Thruster",
        		6 => "Medium Pulse Cannon",
        		8 => "Twin Array",
        		9 => "Heavy Plasma Cannon",
        		18 => "Structure",
        		20 => "Primary",        			
        	),
        	2=> array(
        		7 => "Thruster",
        		9 => "Standard Particle Beam",
        		18 => "Structure",
        		20 => "Primary",        			
        	),
        );
    }
}


?>
