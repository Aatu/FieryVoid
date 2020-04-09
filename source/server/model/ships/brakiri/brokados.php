<?php
class Brokados extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 825;
		$this->faction = "Brakiri";
        $this->phpclass = "Brokados";
        $this->imagePath = "img/ships/brokados.png";
        $this->shipClass = "Brokados Battle Carrier";
        $this->shipSizeClass = 3;
        $this->fighters = array("heavy"=>24);
        
		$this->notes = 'Ly-Nakir Industries';//Corporation producing the design
        $this->isd = 2254;
		
        $this->forwardDefense = 16;
        $this->sideDefense = 17;
        
        $this->turncost = 0.75;
        $this->turndelaycost = 0.5;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->gravitic = true;

        $this->addPrimarySystem(new Reactor(4, 22, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 8, 8));
        $this->addPrimarySystem(new Engine(4, 16, 0, 12, 3));
        $this->addPrimarySystem(new JumpEngine(4, 12, 4, 28));
		$this->addPrimarySystem(new Hangar(4, 30));
        $this->addPrimarySystem(new ShieldGenerator(5, 16, 5, 3));
   
        $this->addFrontSystem(new GravitonPulsar(3, 5, 2, 240, 60));
        $this->addFrontSystem(new HeavyLaser(4, 8, 6, 300, 60));
        $this->addFrontSystem(new HeavyLaser(4, 8, 6, 300, 60));
        $this->addFrontSystem(new GravitonPulsar(3, 5, 2, 300, 120));
        $this->addFrontSystem(new GraviticThruster(5, 10, 0, 4, 1));
        $this->addFrontSystem(new GraviticThruster(5, 10, 0, 4, 1));

        $this->addAftSystem(new GravitonPulsar(3, 5, 2, 120, 300));
        $this->addAftSystem(new GravitonPulsar(3, 5, 2, 60, 240));
        $this->addAftSystem(new GraviticThruster(3, 8, 0, 4, 2));
        $this->addAftSystem(new GraviticThruster(3, 8, 0, 4, 2));
        $this->addAftSystem(new GraviticThruster(3, 8, 0, 4, 2));

        $this->addLeftSystem(new GraviticShield(0, 6, 0, 2, 240, 0));
        $this->addLeftSystem(new GravitonPulsar(3, 5, 2, 180, 0));
        $this->addLeftSystem(new HeavyLaser(4, 8, 6, 240, 0));
        $this->addLeftSystem(new GraviticShield(0, 6, 0, 2, 180, 240));
        $this->addLeftSystem(new GraviticThruster(4, 15, 0, 5, 3));

        $this->addRightSystem(new GraviticShield(0, 6, 0, 2, 0, 120));
        $this->addRightSystem(new GravitonPulsar(3, 5, 2, 0, 180));
        $this->addRightSystem(new HeavyLaser(4, 8, 6, 0, 120));
        $this->addRightSystem(new GraviticShield(0, 6, 0, 2, 120, 180));
        $this->addRightSystem(new GraviticThruster(4, 15, 0, 5, 4));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(5, 42));
        $this->addAftSystem(new Structure(4, 42));
        $this->addLeftSystem(new Structure(4, 48));
        $this->addRightSystem(new Structure(4, 48));
        $this->addPrimarySystem(new Structure(5, 40));
		
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
					3 => "Thruster",
					6 => "Heavy Laser",
					8 => "Graviton Pulsar",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Graviton Pulsar",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					3 => "Thruster",
					6 => "Gravitic Shield",
					8 => "Heavy Laser",
					10 => "Graviton Pulsar",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					3 => "Thruster",
					6 => "Gravitic Shield",
					8 => "Heavy Laser",
					10 => "Graviton Pulsar",
					18 => "Structure",
					20 => "Primary",
			),
		);
		
    }
}
