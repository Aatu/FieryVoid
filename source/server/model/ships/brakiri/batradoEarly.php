<?php
class BatradoEarly extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 460;
	$this->faction = "Brakiri";
        $this->phpclass = "BatradoEarly";
        $this->imagePath = "img/ships/avioki.png";
        $this->shipClass = "Batrado Armed Transport (Early)";
			$this->occurence = "uncommon";
			$this->variantOf = 'Avioki Heavy Cruiser';
        $this->shipSizeClass = 3;
	    $this->isCombatUnit = false; //not a combat unit, it will never be present in a regular battlegroup
		
		$this->notes = 'Tor-Sikar LogTech';//Corporation producing the design
		$this->isd = 2238;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 18;
        
        $this->turncost = 1;
        $this->turndelaycost = 0.5;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->gravitic = true;

        $this->addPrimarySystem(new Reactor(6, 18, 0, 4));
        $this->addPrimarySystem(new CnC(8, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 12, 8, 7));
        $this->addPrimarySystem(new Engine(6, 16, 0, 15, 4));
        $this->addPrimarySystem(new JumpEngine(5, 10, 4, 28));
		$this->addPrimarySystem(new Hangar(5, 4));
   
        $this->addFrontSystem(new GraviticBolt(3, 5, 2, 240, 60));
        $this->addFrontSystem(new GraviticBolt(3, 5, 2, 300, 120));
        $this->addFrontSystem(new GraviticThruster(5, 10, 0, 4, 1));
        $this->addFrontSystem(new GraviticThruster(5, 10, 0, 4, 1));

        $this->addAftSystem(new GraviticBolt(3, 5, 2, 120, 300));
        $this->addAftSystem(new GraviticBolt(3, 5, 2, 60, 240));
        $this->addAftSystem(new GraviticThruster(5, 15, 0, 8, 2));
        $this->addAftSystem(new GraviticThruster(5, 15, 0, 8, 2));

        $this->addLeftSystem(new CargoBay(5, 40));
        $this->addLeftSystem(new GraviticThruster(5, 15, 0, 6, 3));

        $this->addRightSystem(new CargoBay(5, 40));
        $this->addRightSystem(new GraviticThruster(5, 15, 0, 6, 4));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(6, 36));
        $this->addAftSystem(new Structure(6, 36));
        $this->addLeftSystem(new Structure(6, 48));
        $this->addRightSystem(new Structure(6, 48));
        $this->addPrimarySystem(new Structure(6, 44));
		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					10 => "Jump Engine",
					12 => "Scanner",
					15 => "Engine",
					16 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					7 => "Gravitic Bolt",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					9 => "Gravitic Bolt",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					8 => "Cargo Bay",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					8 => "Cargo Bay",
					18 => "Structure",
					20 => "Primary",
			),
		);		
    }
}
