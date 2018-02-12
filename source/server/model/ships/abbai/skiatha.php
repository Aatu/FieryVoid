<?php
class Skiatha extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 690;
	$this->faction = "Abbai";
        $this->phpclass = "Skiatha";
        $this->imagePath = "img/ships/AbbaiSkiatha.png";
        $this->shipClass = "Skiatha Escort Scout";
        $this->shipSizeClass = 3;
        $this->fighters = array("normal"=>12);
        
        $this->limited = 33;
	$this->isd = 2247;
        
        $this->forwardDefense = 17;
        $this->sideDefense = 16;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 4;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(5, 15, 0, 13));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new ELINTScanner(5, 20, 9, 12));
        $this->addPrimarySystem(new Engine(5, 18, 0, 10, 2));
        $this->addPrimarySystem(new ShieldGenerator(5, 16, 4, 3));
        $this->addPrimarySystem(new Hangar(5, 15));

 
        $this->addFrontSystem(new QuadArray(3, 0, 0, 240, 120));
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 3, 300, 360));
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 3, 0, 60));
        $this->addFrontSystem(new Thruster(5, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(5, 10, 0, 3, 1));
	    
        $this->addAftSystem(new GraviticShield(0, 6, 0, 3, 180, 240));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 3, 120, 180));
        $this->addAftSystem(new QuadArray(3, 0, 0, 60, 300));
        $this->addAftSystem(new JumpEngine(4, 16, 5, 32));
        $this->addAftSystem(new Thruster(3, 3, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 3, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 3, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 3, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 3, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 3, 0, 2, 2));

        $this->addLeftSystem(new GraviticShield(0, 6, 0, 3, 240, 300));
        $this->addLeftSystem(new Particleimpeder(3, 0, 0, 180, 360));
        $this->addLeftSystem(new QuadArray(3, 0, 0, 180, 60));
        $this->addLeftSystem(new Thruster(4, 13, 0, 5, 3));

        $this->addRightSystem(new GraviticShield(0, 6, 0, 3, 60, 120));
        $this->addRighSystem(new Particleimpeder(3, 0, 0, 0, 180));
        $this->addRightSystem(new QuadArray(3, 0, 0, 300, 180));
        $this->addRightSystem(new Thruster(4, 13, 0, 5, 4));
	    
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(5, 42));
        $this->addAftSystem(new Structure(4, 44));
        $this->addLeftSystem(new Structure(4, 48));
        $this->addRightSystem(new Structure(4, 48));
        $this->addPrimarySystem(new Structure(5, 40));
		
		$this->hitChart = array(
			0=> array(
					6 => "Structure",
					8 => "Shield Generator",
					11 => "Elint Scanner",
					13 => "Engine",
					16 => "Hangar",
					18 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					2 => "Gravitic Shield",
					6 => "Thruster",
					8 => "Quad Array",
					17 => "Structure",
					20 => "Primary",
			),
			2=> array(
					2 => "Gravitic Shield",
					7 => "Thruster",
					9 => "Quad Array",
					11 => "Jump Drive",
					17 => "Structure",
					20 => "Primary",
			),
			3=> array(
					2 => "Gravitic Shield",
					6 => "Thruster",
					8 => "Quad Array",
					10 => "Particle Impeder",
					17 => "Structure",
					20 => "Primary",
			),
			4=> array(
					2 => "Gravitic Shield",
					6 => "Thruster",
					8 => "Quad Array",
					10 => "Particle Impeder",
					17 => "Structure",
					20 => "Primary",
			),
		);
    }
}
?>
