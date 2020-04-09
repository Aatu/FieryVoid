<?php
class Kabrik extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 350;
	$this->faction = "Brakiri";
        $this->phpclass = "Kabrik";
        $this->imagePath = "img/ships/kabrik.png";
        $this->shipClass = "Kabrik Police Ship";
        $this->agile = true;
        $this->gravitic = true;
        $this->canvasSize = 100;
	    $this->fighters = array("assault shuttles"=>6); //4 Assault Shuttles and 2 Breaching Pods, by design
        
		$this->notes = 'Pri-Wakat Concepts & Solutions';//Corporation producing the design
		$this->isd = 2241;
		
        $this->forwardDefense = 12;
        $this->sideDefense = 13;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 1;
	$this->iniativebonus = 60;

        $this->addPrimarySystem(new Reactor(4, 10, 0, 0));
        $this->addPrimarySystem(new CnC(5, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 8, 4, 6));
        $this->addPrimarySystem(new Engine(4, 7, 0, 7, 2));
	$this->addPrimarySystem(new Hangar(3, 7));
	$this->addPrimarySystem(new GraviticThruster(4, 8, 0, 4, 3));
	$this->addPrimarySystem(new GraviticThruster(4, 8, 0, 4, 4));
		
        $this->addFrontSystem(new GraviticBolt(3, 5, 2, 240, 60));
        $this->addFrontSystem(new GraviticBolt(3, 5, 2, 300, 120));
        $this->addFrontSystem(new GraviticCannon(3, 6, 5, 270, 90));
        $this->addFrontSystem(new GraviticThruster(4, 6, 0, 2, 1));
        $this->addFrontSystem(new GraviticThruster(4, 6, 0, 2, 1));
        
        $this->addAftSystem(new GraviticBolt(3, 5, 2, 90, 270));
        $this->addAftSystem(new GraviticBolt(3, 5, 2, 90, 270));
        $this->addAftSystem(new GraviticThruster(4, 15, 0, 7, 2));
		
        $this->addPrimarySystem(new Structure( 4, 48));
		
		$this->hitChart = array(
			0=> array(
					8 => "Thruster",
					11 => "Scanner",
					14 => "Engine",
					15 => "Hangar",
					18 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					6 => "Gravitic Cannon",
					10 => "Gravitic Bolt",
					16 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Gravitic Bolt",
					16 => "Structure",
					20 => "Primary",
			),
		);
		
    }
}
?>
