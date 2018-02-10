<?php
class Milani extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 500;
	$this->faction = "Abbai";
        $this->phpclass = "Milani";
        $this->imagePath = "img/ships/AbbaiMilani.png";
        $this->shipClass = "Milani Carrier";
        $this->shipSizeClass = 3;
        $this->fighters = array("normal"=>24);
        
	//$this->occurence = "uncommon";
        //$this->variantOf = 'Milani Carrier';
	$this->isd = 2230;
        
        $this->forwardDefense = 16;
        $this->sideDefense = 16;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(4, 17, 0, 8));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 14, 6, 7));
        $this->addPrimarySystem(new Engine(4, 16, 0, 8, 3));
        $this->addPrimarySystem(new ShieldGenerator(4, 14, 4, 4));
   
        $this->addFrontSystem(new CombatLaser(3, 0, 0, 300, 60));
        $this->addFrontSystem(new QuadArray(3, 0, 0, 240, 60));
        $this->addFrontSystem(new QuadArray(3, 0, 0, 300, 120));
        $this->addFrontSystem(new Particleimpeder(2, 0, 0, 240, 60));
        $this->addFrontSystem(new Particleimpeder(2, 0, 0, 300, 120));
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 3, 300, 360));
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 3, 0, 60));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
	    
        $this->addAftSystem(new Particleimpeder(2, 0, 0, 120, 300));
        $this->addAftSystem(new Particleimpeder(2, 0, 0, 60, 240));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 2, 180, 240));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 2, 120, 180));
        $this->addAftSystem(new Thruster(3, 19, 0, 8, 2));

        $this->addLeftSystem(new GraviticShield(0, 6, 0, 3, 240, 300));
        $this->addLeftSystem(new Hangar(3, 13));
        $this->addLeftSystem(new QuadArray(3, 0, 0, 180, 360));
        $this->addLeftSystem(new Thruster(3, 13, 0, 6, 3));

        $this->addRightSystem(new GraviticShield(0, 6, 0, 3, 60, 120));
        $this->addRightSystem(new Hangar(3, 13));
        $this->addRightSystem(new QuadArray(3, 0, 0, 0, 180));
        $this->addRightSystem(new Thruster(3, 13, 0, 6, 4));
	    
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(3, 30));
        $this->addAftSystem(new Structure(3, 30));
        $this->addLeftSystem(new Structure(3, 40));
        $this->addRightSystem(new Structure(3, 40));
        $this->addPrimarySystem(new Structure(4, 32));
		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					10 => "Shield Generator",
					13 => "Scanner",
					16 => "Engine",
					18 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					6 => "Gravitic Shield",
					7 => "Combat Laser",
					9 => "Particle Impeder",
					11 => "Quad Array",
					17 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Gravitic Shield",
					10 => "Particle Impeder",
					17 => "Structure",
					20 => "Primary",
			),
			3=> array(
					5 => "Thruster",
					7 => "Gravitic Shield",
					9 => "Quad Array",
					17 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					7 => "Gravitic Shield",
					9 => "Quad Array",
					17 => "Structure",
					20 => "Primary",
			),
		);
    }
}
?>
