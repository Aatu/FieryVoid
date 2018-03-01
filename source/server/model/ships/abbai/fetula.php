<?php
class Fetula extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 470;
	$this->faction = "Abbai";
        $this->phpclass = "Fetula";
        $this->imagePath = "img/ships/AbbaiShyarie.png";
        $this->shipClass = "Fetula Warrant Cutter";
        $this->canvasSize = 100;
        $this->fighters = array("Breaching Pods" => 3);
	
        $this->occurence = "rare";
        $this->variantOf = 'Shyarie Jammer Frigate'; 
	$this->isd = 2180;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 1;
	$this->iniativebonus = +60;
		
        $this->addPrimarySystem(new Reactor(4, 12, 0, 8));
        $this->addPrimarySystem(new CnC(5, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 14, 6, 7));
        $this->addPrimarySystem(new Engine(4, 12, 0, 9, 2));
	$this->addPrimarySystem(new Hangar(2, 6));
        $this->addPrimarySystem(new ShieldGenerator(4, 10, 3, 3));
	$this->addPrimarySystem(new Thruster(3, 13, 0, 5, 3));
	$this->addPrimarySystem(new Thruster(3, 13, 0, 5, 4));
		
        $this->addFrontSystem(new CommDisruptor(3, 0, 0, 240, 60));
        $this->addFrontSystem(new QuadArray(3, 0, 0, 270, 90));
        $this->addFrontSystem(new CommDisruptor(3, 0, 0, 300, 120));
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 2, 240, 360));
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 2, 0, 120));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));

        $this->addAftSystem(new QuadArray(3, 0, 0, 180, 360));	
        $this->addAftSystem(new QuadArray(3, 0, 0, 0, 180));	
        $this->addAftSystem(new Particleimpeder(2, 0, 0, 180, 360));
        $this->addAftSystem(new Particleimpeder(2, 0, 0, 0, 180));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 2, 180, 240));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 2, 120, 180));
        $this->addAftSystem(new Thruster(3, 8, 0, 5, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 5, 2));
       
        $this->addPrimarySystem(new Structure(4, 54));
        
        $this->hitChart = array(
        		0=> array(
					7 => "Thruster",
					9 => "Shield Generator",
					12 => "Scanner",
					15 => "Engine",
					16 => "Hangar",
					18 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					6 => "Gravitic Shield",
					8 => "Comm Disruptor",
					10 => "Quad Array",
					16 => "Structure",
					20 => "Primary",
			),
			2=> array(
					3 => "Thruster",
					5 => "Gravitic Shield",
					7 => "Quad Array",
					9 => "Particle Impeder",
					16 => "Structure",
					20 => "Primary",
        		),
        );
    }
}
?>
