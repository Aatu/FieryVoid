<?php
class Marata extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 550;
	$this->faction = "Abbai";
        $this->phpclass = "Marata";
        $this->imagePath = "img/ships/AbbaiMilani.png";
        $this->shipClass = "Marata Diplomatic Transport";
        $this->shipSizeClass = 3;
        $this->fighters = array("normal"=>12);
        
	$this->occurence = "rare";
        $this->variantOf = 'Milani Carrier';
	$this->isd = 2233;
        
        $this->forwardDefense = 16;
        $this->sideDefense = 16;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 5;
        
        $this->addPrimarySystem(new Reactor(5, 20, 0, 12));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 7, 9));
        $this->addPrimarySystem(new Engine(5, 18, 0, 9, 3));
        $this->addPrimarySystem(new ShieldGenerator(5, 16, 4, 5));
		$cA = new CargoBay(5, 12);
		$cB = new CargoBay(5, 12);
		$cA->displayName = "Quarters";
		$cB->displayName = "Quarters";
 
        $this->addFrontSystem(new QuadArray(3, 0, 0, 240, 60));
        $this->addFrontSystem(new QuadArray(3, 0, 0, 300, 120));
        $this->addFrontSystem(new Particleimpeder(3, 0, 0, 240, 60));
        $this->addFrontSystem(new Particleimpeder(3, 0, 0, 270, 90));
        $this->addFrontSystem(new Particleimpeder(3, 0, 0, 300, 120));
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 4, 300, 360));
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 4, 0, 60));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
	    
        $this->addAftSystem(new Particleimpeder(3, 0, 0, 120, 300));
        $this->addAftSystem(new Particleimpeder(3, 0, 0, 90, 270));
        $this->addAftSystem(new Particleimpeder(3, 0, 0, 60, 240));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 4, 180, 240));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 4, 120, 180));
        $this->addAftSystem(new Thruster(4, 21, 0, 9, 2));

        $this->addLeftSystem(new GraviticShield(0, 6, 0, 4, 240, 300));
        $this->addLeftSystem(new Hangar(3, 9));
        $this->addLeftSystem(new QuadArray(3, 0, 0, 180, 360));
        $this->addLeftSystem(new Thruster(4, 13, 0, 5, 3));

        $this->addRightSystem(new GraviticShield(0, 6, 0, 4, 60, 120));
        $this->addRightSystem(new Hangar(3, 9));
        $this->addRightSystem(new QuadArray(3, 0, 0, 0, 180));
        $this->addRightSystem(new Thruster(4, 13, 0, 5, 4));
	    
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 34));
        $this->addAftSystem(new Structure(4, 32));
        $this->addLeftSystem(new Structure(4, 44));
        $this->addRightSystem(new Structure(4, 44));
        $this->addPrimarySystem(new Structure(5, 32));
		
		$this->hitChart = array(
			0=> array(
					6 => "Structure",
					8 => "Quarters",
					10 => "Shield Generator",
					13 => "Scanner",
					16 => "Engine",
					18 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					6 => "Gravitic Shield",
					9 => "Particle Impeder",
					11 => "Quad Array",
					17 => "Structure",
					20 => "Primary",
			),
			2=> array(
					5 => "Thruster",
					7 => "Gravitic Shield",
					10 => "Particle Impeder",
					17 => "Structure",
					20 => "Primary",
			),
			3=> array(
					5 => "Thruster",
					7 => "Gravitic Shield",
					9 => "Quad Array",
					11 => "Hangar",
					17 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					7 => "Gravitic Shield",
					9 => "Quad Array",
					11 => "Hangar",
					17 => "Structure",
					20 => "Primary",
			),
		);
    }
}
?>
