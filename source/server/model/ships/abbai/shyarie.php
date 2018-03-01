<?php
class Shyarie extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 540;
	$this->faction = "Abbai";
        $this->phpclass = "Shyarie";
        $this->imagePath = "img/ships/AbbaiShyarie.png";
        $this->shipClass = "Shyarie Jammer Frigate";
        $this->canvasSize = 100;
	
        $this->limited = 10;    
	$this->isd = 2180;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 1;
	$this->iniativebonus = +60;
		
        $this->addPrimarySystem(new Reactor(4, 12, 0, 10));
        $this->addPrimarySystem(new CnC(5, 9, 0, 0));
        $this->addPrimarySystem(new ELINTScanner(4, 17, 6, 9));
        $this->addPrimarySystem(new Engine(4, 12, 0, 9, 2));
	$this->addPrimarySystem(new Hangar(3, 1));
        $this->addPrimarySystem(new ShieldGenerator(4, 10, 3, 3));
	$this->addPrimarySystem(new Thruster(3, 13, 0, 5, 3));
	$this->addPrimarySystem(new Thruster(3, 13, 0, 5, 4));
		
        $this->addFrontSystem(new CommDisruptor(3, 0, 0, 240, 360));
        $this->addFrontSystem(new CommDisruptor(3, 0, 0, 240, 120));
        $this->addFrontSystem(new CommDisruptor(3, 0, 0, 0, 120));
        $this->addFrontSystem(new Particleimpeder(2, 0, 0, 240, 60));
        $this->addFrontSystem(new Particleimpeder(2, 0, 0, 300, 120));
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 2, 240, 360));
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 2, 0, 120));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));

        $this->addAftSystem(new CommDisruptor(3, 0, 0, 180, 300));	
        $this->addAftSystem(new CommDisruptor(3, 0, 0, 60, 180));		
        $this->addAftSystem(new Particleimpeder(2, 0, 0, 120, 300));
        $this->addAftSystem(new Particleimpeder(2, 0, 0, 60, 240));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 2, 180, 240));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 2, 120, 180));
        $this->addAftSystem(new Thruster(3, 8, 0, 5, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 5, 2));
       
        $this->addPrimarySystem(new Structure(4, 54));
        
        $this->hitChart = array(
        		0=> array(
					7 => "Thruster",
					9 => "Shield Generator",
					12 => "Elint Scanner",
					15 => "Engine",
					16 => "Hangar",
					18 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					3 => "Thruster",
					5 => "Gravitic Shield",
					8 => "Comm Disruptor",
					10 => "Particle Impeder",
					16 => "Structure",
					20 => "Primary",
			),
			2=> array(
					3 => "Thruster",
					5 => "Gravitic Shield",
					7 => "Comm Disruptor",
					9 => "Particle Impeder",
					16 => "Structure",
					20 => "Primary",
        		),
        );
    }
}
?>
