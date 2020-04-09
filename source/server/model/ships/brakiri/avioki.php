<?php
class Avioki extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 725;
	$this->faction = "Brakiri";
        $this->phpclass = "Avioki";
        $this->imagePath = "img/ships/avioki.png";
        $this->shipClass = "Avioki Heavy Cruiser";
        $this->shipSizeClass = 3;
		
		$this->notes = 'Ak-Habil Conglomerate';//Corporation producing the design
		$this->isd = 2204;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 18;
        
        $this->turncost = 1;
        $this->turndelaycost = 0.5;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->gravitic = true;

        $this->addPrimarySystem(new Reactor(6, 22, 0, 0));
        $this->addPrimarySystem(new CnC(8, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 13, 8, 8));
        $this->addPrimarySystem(new Engine(6, 16, 0, 15, 4));
        $this->addPrimarySystem(new JumpEngine(5, 12, 4, 28));
	$this->addPrimarySystem(new Hangar(5, 2));
   
        $this->addFrontSystem(new GravitonPulsar(3, 5, 2, 240, 60));
        $this->addFrontSystem(new GravitonPulsar(3, 5, 2, 300, 120));
        $this->addFrontSystem(new GraviticThruster(5, 10, 0, 4, 1));
        $this->addFrontSystem(new GraviticThruster(5, 10, 0, 4, 1));

        $this->addAftSystem(new GravitonPulsar(3, 5, 2, 120, 300));
        $this->addAftSystem(new GravitonPulsar(3, 5, 2, 60, 240));
        $this->addAftSystem(new GraviticThruster(5, 15, 0, 8, 2));
        $this->addAftSystem(new GraviticThruster(5, 15, 0, 8, 2));

        $this->addLeftSystem(new GravitonBeam(5, 8, 8, 300, 360));
        $this->addLeftSystem(new GravitonBeam(5, 8, 8, 300, 360));
        $this->addLeftSystem(new GraviticThruster(5, 15, 0, 6, 3));

        $this->addRightSystem(new GravitonBeam(5, 8, 8, 0, 60));
        $this->addRightSystem(new GravitonBeam(5, 8, 8, 0, 60));
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
					7 => "Graviton Pulsar",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					9 => "Graviton Pulsar",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					8 => "Graviton Beam",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					8 => "Graviton Beam",
					18 => "Structure",
					20 => "Primary",
			),
		);		
    }
}