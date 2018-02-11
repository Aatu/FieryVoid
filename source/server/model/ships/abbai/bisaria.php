<?php
class Bisaria extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 500;
	$this->faction = "Abbai";
        $this->phpclass = "Bisaria";
        $this->imagePath = "img/ships/AbbaiTiraca.png";
        $this->shipClass = "Bisaria Escort Frigate";
        $this->canvasSize = 100;
	    
        $this->occurence = "uncommon";
        $this->variantOf = 'Tiraca Attack Frigate';
	$this->isd = 2235;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
	$this->agile = true;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.3;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
	$this->iniativebonus = +60;
		
        $this->addPrimarySystem(new Reactor(4, 12, 0, 8));
        $this->addPrimarySystem(new CnC(5, 14, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 13, 5, 7));
        $this->addPrimarySystem(new Engine(4, 12, 0, 9, 2));
	$this->addPrimarySystem(new Hangar(3, 1));
        $this->addPrimarySystem(new ShieldGenerator(4, 12, 3, 2));
	$this->addPrimarySystem(new Thruster(3, 13, 0, 5, 3));
	$this->addPrimarySystem(new Thruster(3, 13, 0, 5, 4));
		
        $this->addFrontSystem(new CommDisruptor(3, 0, 0, 300, 60));
        $this->addFrontSystem(new QuadArray(2, 0, 0, 180, 60));
        $this->addFrontSystem(new QuadArray(2, 0, 0, 300, 60));
        $this->addFrontSystem(new QuadArray(2, 0, 0, 300, 180));
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 3, 240, 360));
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 3, 0, 120));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
		
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
					7 => "Comm Disruptor",
					10 => "Quad Array",
					16 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Gravitic Shield",	
					10 => "Particle Impeder",
					16 => "Structure",
					20 => "Primary",
        		),
        );
    }
}
?>
