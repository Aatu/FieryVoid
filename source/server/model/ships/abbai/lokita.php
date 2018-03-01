<?php
class Lokita extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 650;
	$this->faction = "Abbai";
        $this->phpclass = "Lokita";
        $this->imagePath = "img/ships/AbbaiLakara.png";
        $this->shipClass = "Lokita Carrier";
        $this->shipSizeClass = 3;
        $this->fighters = array("normal"=>12);

        $this->occurence = "uncommon";
        $this->variantOf = 'Lakara Cruiser';
	$this->isd = 2243;
        
        $this->forwardDefense = 18;
        $this->sideDefense = 16;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(5, 17, 0, 10));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 18, 6, 9));
        $this->addPrimarySystem(new Engine(5, 16, 0, 10, 3));
 	$this->addPrimarySystem(new Hangar(5, 2));
        $this->addPrimarySystem(new ShieldGenerator(5, 16, 4, 3));
   

        $this->addFrontSystem(new CombatLaser(3, 0, 0, 300, 60));
        $this->addFrontSystem(new CombatLaser(3, 0, 0, 300, 60));
        $this->addFrontSystem(new QuadArray(3, 0, 0, 240, 60));
        $this->addFrontSystem(new QuadArray(3, 0, 0, 300, 120));
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 3, 300, 360));
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 3, 0, 60));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
	    


        $this->addAftSystem(new JumpEngine(4, 14, 4, 32));
        $this->addAftSystem(new QuadArray(3, 0, 0, 90, 270));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 3, 180, 240));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 3, 120, 180));
        $this->addAftSystem(new Thruster(4, 12, 0, 5, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 5, 2));

        $this->addLeftSystem(new GraviticShield(0, 6, 0, 2, 240, 300));
        $this->addLeftSystem(new Hangar(3, 6));
        $this->addLeftSystem(new Particleimpeder(2, 0, 0, 180, 360));
        $this->addLeftSystem(new Thruster(3, 13, 0, 6, 3));

        $this->addRightSystem(new GraviticShield(0, 6, 0, 2, 60, 120));
        $this->addRightSystem(new Hangar(3, 6));
        $this->addRightSystem(new Particleimpeder(2, 0, 0, 0, 180));
        $this->addRightSystem(new Thruster(3, 13, 0, 6, 4));
	    
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 36));
        $this->addAftSystem(new Structure(4, 36));
        $this->addLeftSystem(new Structure(4, 44));
        $this->addRightSystem(new Structure(4, 44));
        $this->addPrimarySystem(new Structure(5, 36));
		
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
					8 => "Combat Laser",
					10 => "Quad Array",
					17 => "Structure",
					20 => "Primary",
			),
			2=> array(
					5 => "Thruster",
					7 => "Gravitic Shield",
					8 => "Quad Array",	
					11 => "Jump Engine",
					17 => "Structure",
					20 => "Primary",
			),
			3=> array(
					3 => "Thruster",
					4 => "Gravitic Shield",
					6 => "Hangar",
					8 => "Particle Impeder",
					17 => "Structure",
					20 => "Primary",
			),
			4=> array(
					3 => "Thruster",
					4 => "Gravitic Shield",
					6 => "Hangar",
					8 => "Particle Impeder",
					17 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
