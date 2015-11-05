<?php
class Barque extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 525;
        $this->faction = "Raiders";
        $this->phpclass = "Barque";
        $this->imagePath = "img/ships/brigantine.png"; //need to change
        $this->shipClass = "Barque";
        $this->occurence = "common";
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
		$this->addFrontSystem(new TwinArray(2, 6, 2, 270, 120));
		$this->addFrontSystem(new HeavyPlasma(3, 8, 5, 270, 60));		
        
        $this->addAftSystem(new Thruster(3, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 3, 2));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 180, 270));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 60, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 50));
        $this->addPrimarySystem(new Structure( 4, 50));
        $this->addAftSystem(new Structure( 4, 42));
        
        $this->hitChart = array(
        	0=> array(
        		1 => "structure",
        		2 => "structure",
        		3 => "structure",
        		4 => "structure",
        		5 => "structure",
        		6 => "structure",
        		7 => "structure",
        		8 => "cargoBay",
        		9 => "cargoBay",
        		10 => "thruster",
        		11 => "thruster",
        		12 => "thruster",
        		13 => "scanner",
        		14 => "scanner",
        		15 => "engine",
        		16 => "engine",
        		17 => "hanger",
        		18 => "reactor",
        		19 => "reactor",
        		20 => "CnC",	
        	),
        	1=> array(
        		1 => "thruster",
        		2 => "thruster",
        		3 => "thruster",
        		4 => "thruster",
        		5 => "mediumPulse",
        		6 => "mediumPulse",
        		7 => "twinArray",
        		8 => "twinArray",
        		9 => "heavyPlasma",
        		10 => "structure",
        		11 => "structure",
        		12 => "structure",
        		13 => "structure",
        		14 => "structure",
        		15 => "structure",
        		16 => "structure",
        		17 => "structure",
        		18 => "structure",
        		19 => "primary",
        		20 => "primary",        			
        	),
        	2=> array(
        		1 => "thruster",
        		2 => "thruster",
        		3 => "thruster",
        		4 => "thruster",
        		5 => "thruster",
        		6 => "thruster",
        		7 => "thruster",
        		8 => "stdParticleBeam",
        		9 => "stdParticleBeam",
        		10 => "structure",
        		11 => "structure",
        		12 => "structure",
        		13 => "structure",
        		14 => "structure",
        		15 => "structure",
        		16 => "structure",
        		17 => "structure",
        		18 => "structure",
        		19 => "primary",
        		20 => "primary",        			
        	),
        );
    }
}


?>
