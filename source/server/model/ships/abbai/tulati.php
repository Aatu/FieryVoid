<?php
class Tulati extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 300;
	$this->faction = "Abbai";
        $this->phpclass = "Tulati";
        $this->imagePath = "img/ships/AbbaiLyata.png";
        $this->shipClass = "Tulati Frigate";
        $this->canvasSize = 100;
	    
	$this->isd = 2180;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
	$this->agile = true;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 1;
	$this->iniativebonus = +60;
		
        $this->addPrimarySystem(new Reactor(4, 10, 0, 7));
        $this->addPrimarySystem(new CnC(5, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 4, 6));
        $this->addPrimarySystem(new Engine(4, 11, 0, 6, 2));
	$this->addPrimarySystem(new Hangar(3, 1));
        $this->addPrimarySystem(new ShieldGenerator(4, 8, 2, 1));
	$this->addPrimarySystem(new Thruster(3, 11, 0, 4, 3));
	$this->addPrimarySystem(new Thruster(3, 11, 0, 4, 4));
		
        $this->addFrontSystem(new QuadArray(2, 0, 0, 180, 60));
        $this->addFrontSystem(new QuadArray(2, 0, 0, 240, 180));
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 2, 270, 90));
        $this->addFrontSystem(new Thruster(3, 13, 0, 4, 1));
		
        $this->addAftSystem(new Particleimpeder(2, 0, 0, 180, 360));
        $this->addAftSystem(new Particleimpeder(2, 0, 0, 0, 180));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 2, 90, 270));
        $this->addAftSystem(new Thruster(3, 14, 0, 6, 2));
       
        $this->addPrimarySystem(new Structure(4, 48));
        
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
