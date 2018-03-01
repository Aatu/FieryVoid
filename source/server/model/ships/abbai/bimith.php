<?php
class Bimith extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 550;
	$this->faction = "Abbai";
        $this->phpclass = "Bimith";
        $this->imagePath = "img/ships/AbbaiBimith.png";
        $this->shipClass = "Bimith Defender";
        $this->shipSizeClass = 3;
	$this->isd = 2246;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        $this->iniativebonus = 5;
        
        $this->addPrimarySystem(new Reactor(4, 16, 0, 9));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 6, 8));
        $this->addPrimarySystem(new Engine(4, 16, 0, 8, 3));
 	$this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new ShieldGenerator(4, 16, 4, 4));
   
        $this->addFrontSystem(new Particleimpeder(2, 0, 0, 270, 90));
        $this->addFrontSystem(new QuadArray(3, 0, 0, 240, 60));
        $this->addFrontSystem(new QuadArray(3, 0, 0, 300, 120));
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 3, 300, 360));
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 3, 0, 60));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));

        $this->addAftSystem(new Particleimpeder(2, 0, 0, 90, 270));
        $this->addAftSystem(new QuadArray(3, 0, 0, 120, 240));
        $this->addAftSystem(new QuadArray(3, 0, 0, 120, 240));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 3, 180, 240));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 3, 120, 180));
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));

        $this->addLeftSystem(new GraviticShield(0, 6, 0, 3, 240, 300));
        $this->addLeftSystem(new QuadArray(3, 0, 0, 180, 360));
        $this->addLeftSystem(new Particleimpeder(2, 0, 0, 180, 360));
        $this->addLeftSystem(new Thruster(3, 15, 0, 5, 3));

        $this->addRightSystem(new GraviticShield(0, 6, 0, 3, 60, 120));
        $this->addRightSystem(new QuadArray(3, 0, 0, 0, 180));
        $this->addRightSystem(new Particleimpeder(2, 0, 0, 0, 180));
        $this->addRightSystem(new Thruster(3, 15, 0, 5, 4));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 32));
        $this->addAftSystem(new Structure(4, 32));
        $this->addLeftSystem(new Structure(4, 40));
        $this->addRightSystem(new Structure(4, 40));
        $this->addPrimarySystem(new Structure(4, 36));
		
		$this->hitChart = array(
			0=> array(
					7 => "Structure",
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
					9 => "Quad Array",
					10 => "Particle Impeder",
					17 => "Structure",
					20 => "Primary",
			),
			2=> array(
					4 => "Thruster",
					6 => "Gravitic Shield",	
					9 => "Quad Array",
					11 => "Particle Impeder",
					17 => "Structure",
					20 => "Primary",
			),
			3=> array(
					3 => "Thruster",
					4 => "Gravitic Shield",
					6 => "Quad Array",
					8 => "Particle Impeder",
					17 => "Structure",
					20 => "Primary",
			),
			4=> array(
					3 => "Thruster",
					4 => "Gravitic Shield",
					6 => "Quad Array",
					8 => "Particle Impeder",
					17 => "Structure",
					20 => "Primary",
			),
		);
    }
}
?>
