<?php
class Lyata extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 350;
	$this->faction = "Abbai";
        $this->phpclass = "Lyata";
        $this->imagePath = "img/ships/AbbaiLyata.png";
        $this->shipClass = "Lyata Police Corvette";
        $this->canvasSize = 100;
	    
	$this->isd = 2238;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 13;
        
	$this->agile = true;
        $this->turncost = 0.25;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 1;
	$this->iniativebonus = +70;
		
        $this->addPrimarySystem(new Reactor(4, 10, 0, 7));
        $this->addPrimarySystem(new CnC(4, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 4, 6));
        $this->addPrimarySystem(new Engine(4, 11, 0, 8, 2));
	$this->addPrimarySystem(new Hangar(4, 1));
        $this->addPrimarySystem(new ShieldGenerator(4, 8, 3, 1));
	$this->addPrimarySystem(new Thruster(3, 11, 0, 4, 3));
	$this->addPrimarySystem(new Thruster(3, 11, 0, 4, 4));
		
        $this->addFrontSystem(new CombatLaser(3, 0, 0, 300, 60));
        $this->addFrontSystem(new QuadArray(2, 0, 0, 240, 120));
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 2, 270, 90));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
		
        $this->addAftSystem(new Particleimpeder(2, 0, 0, 180, 360));
        $this->addAftSystem(new Particleimpeder(2, 0, 0, 0, 180));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 2, 90, 270));
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 4, 2));
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
       
        $this->addPrimarySystem(new Structure(4, 36));
        
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
					5 => "Gravitic Shield",
					7 => "Combat Laser",
					9 => "Quad Array",
					16 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					7 => "Gravitic Shield",	
					9 => "Particle Impeder",
					16 => "Structure",
					20 => "Primary",
        		),
        );
    }
}
?>
