<?php
class Ikorta extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 450;
	$this->faction = "Brakiri";
        $this->phpclass = "Ikorta";
        $this->imagePath = "img/ships/ikorta.png";
        $this->shipClass = "Ikorta Light Assault Cruiser";
	    $this->fighters = array("assault shuttles"=>6);
        
		$this->notes = 'Pri-Wakat Concepts & Solutions';//Corporation producing the design
		$this->isd = 2250;
		
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;

        $this->gravitic = true;

        $this->addPrimarySystem(new Reactor(7, 18, 0, 0));
        $this->addPrimarySystem(new CnC(8, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 12, 6, 8));
        $this->addPrimarySystem(new Engine(6, 14, 0, 10, 3));
        $this->addPrimarySystem(new Hangar(6, 8));
	$this->addPrimarySystem(new ShieldGenerator(6, 10, 2, 1));
        $this->addPrimarySystem(new GraviticThruster(6, 15, 0, 6, 1));
        $this->addPrimarySystem(new GraviticThruster(6, 18, 0, 10, 2));
        $this->addPrimarySystem(new GravitonPulsar(4, 5, 2, 90, 270));

        $this->addLeftSystem(new GravitonPulsar(4, 5, 2, 240, 60));
        $this->addLeftSystem(new GraviticCannon(5, 6, 5, 240, 0));
        $this->addLeftSystem(new GraviticThruster(6, 15, 0, 6, 3));
        $this->addLeftSystem(new GraviticShield(0, 6, 0, 3, 180, 360));

        $this->addRightSystem(new GravitonPulsar(4, 5, 2, 300, 120));
        $this->addRightSystem(new GraviticCannon(5, 6, 5, 0, 120));
        $this->addRightSystem(new GraviticThruster(6, 15, 0, 6, 4));
        $this->addRightSystem(new GraviticShield(0, 6, 0, 3, 0, 180));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(7, 40));
        $this->addLeftSystem(new Structure(6, 48));
        $this->addRightSystem(new Structure(6, 48));
		
		$this->hitChart = array(
			0=> array(
					7 => "Structure",
					9 => "Shield Generator",
					11 => "Thruster",
					12 => "Graviton Pulsar",
					14 => "Scanner",
					16 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			3=> array(
					4 => "Thruster",
					6 => "Gravitic Shield",
					8 => "Gravitic Cannon",
					10 => "Graviton Pulsar",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					6 => "Gravitic Shield",
					8 => "Gravitic Cannon",					
					10 => "Graviton Pulsar",
					18 => "Structure",
					20 => "Primary",
			),
		);
		
    }
}
