<?php
class Devaskar extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 600;
	$this->faction = "Brakiri";
        $this->phpclass = "Devaskar";
        $this->imagePath = "img/ships/devaskar.png";
        $this->shipClass = "Devaskar Carrier";
        $this->shipSizeClass = 3;
        $this->fighters = array("normal"=>6, "light"=>12);
        
        $this->forwardDefense = 16;
        $this->sideDefense = 16;
        
		$this->notes = 'Ly-Nakir Industries';//Corporation producing the design
		$this->isd = 2228;
        
        $this->turncost = 1;
        $this->turndelaycost = 0.75;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->gravitic = true;

        $this->addPrimarySystem(new Reactor(4, 18, 0, 0));
        $this->addPrimarySystem(new CnC(4, 14, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 8, 7));
        $this->addPrimarySystem(new Engine(4, 14, 0, 10, 3));
        $this->addPrimarySystem(new JumpEngine(4, 12, 4, 32));
	$this->addPrimarySystem(new Hangar(4, 20));
        $this->addPrimarySystem(new ShieldGenerator(4, 12, 4, 2));
   
        $this->addFrontSystem(new GraviticBolt(3, 5, 2, 240, 60));
        $this->addFrontSystem(new GraviticBolt(3, 5, 2, 240, 60));
        $this->addFrontSystem(new GraviticThruster(4, 8, 0, 4, 1));
        $this->addFrontSystem(new GraviticThruster(4, 8, 0, 4, 1));
        $this->addFrontSystem(new GraviticBolt(3, 5, 2, 300, 120));
        $this->addFrontSystem(new GraviticBolt(3, 5, 2, 300, 120));

        $this->addAftSystem(new GraviticBolt(3, 5, 2, 60, 240));
        $this->addAftSystem(new GraviticBolt(3, 5, 2, 120, 300));
        $this->addAftSystem(new GraviticThruster(3, 16, 0, 5, 2));
        $this->addAftSystem(new GraviticThruster(3, 16, 0, 5, 2));

        $this->addLeftSystem(new GraviticShield(0, 6, 0, 2, 240, 0));
        $this->addLeftSystem(new GraviticBolt(3, 5, 2, 240, 60));
        $this->addLeftSystem(new GraviticBolt(3, 5, 2, 120, 300));
        $this->addLeftSystem(new GraviticShield(0, 6, 0, 2, 180, 240));
        $this->addLeftSystem(new GraviticThruster(4, 13, 0, 5, 3));

        $this->addRightSystem(new GraviticShield(0, 6, 0, 2, 0, 120));
        $this->addRightSystem(new GraviticBolt(3, 5, 2, 300, 120));
        $this->addRightSystem(new GraviticBolt(3, 5, 2, 60, 240));
        $this->addRightSystem(new GraviticShield(0, 6, 0, 2, 120, 180));
        $this->addRightSystem(new GraviticThruster(4, 13, 0, 5, 4));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 36));
        $this->addAftSystem(new Structure(4, 36));
        $this->addLeftSystem(new Structure(4, 44));
        $this->addRightSystem(new Structure(4, 44));
        $this->addPrimarySystem(new Structure(4, 36));
		
		$this->hitChart = array(
			0=> array(
					6 => "Structure",
					8 => "Shield Generator",
					10 => "Jump Engine",
					12 => "Scanner",
					14 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					8 => "Gravitic Bolt",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Graviton Beam",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					7 => "Gravitic Shield",
					9 => "Gravitic Bolt",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					7 => "Gravitic Shield",
					9 => "Gravitic Bolt",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}
