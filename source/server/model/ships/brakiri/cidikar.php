<?php
class cidikar extends BaseShip{
    /*Brakiri Cidikar Heavy Carrier, Ly-Nakir Industries - Ships of the Fleet*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 800;
	$this->faction = "Brakiri";
        $this->phpclass = "cidikar";
        $this->imagePath = "img/ships/BrakiriCidikar.png";
        $this->shipClass = "Cidikar Heavy Carrier";
        $this->shipSizeClass = 3;
        $this->fighters = array("heavy"=>24, "light"=>24);

        $this->limited = 10; //only 3 exist

        
		$this->notes = 'Ly-Nakir Industries';//Corporation producing the design
		$this->isd = 2246;
		
        $this->forwardDefense = 17;
        $this->sideDefense = 19;
        


        $this->turncost = 1;
        $this->turndelaycost = 0.66;
        $this->accelcost = 5;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0*5;
        $this->gravitic = true;

        $this->addPrimarySystem(new Reactor(5, 22, 0, 0));
        $this->addPrimarySystem(new CnC(8, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 18, 8, 8));
        $this->addPrimarySystem(new Engine(5, 18, 0, 15, 4));
	$this->addPrimarySystem(new ShieldGenerator(5, 16, 5, 4));
        $this->addPrimarySystem(new JumpEngine(4, 16, 4, 24));
	$this->addPrimarySystem(new Hangar(5, 26, 12));
   

	$this->addFrontSystem(new GraviticShield(0, 6, 0, 2, 300, 0));
        $this->addFrontSystem(new GravitonPulsar(4, 5, 2, 300, 60));
        $this->addFrontSystem(new GravitonPulsar(4, 5, 2, 300, 60));
	$this->addFrontSystem(new GraviticShield(0, 6, 0, 2, 0, 60));
        $this->addFrontSystem(new GraviticThruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new GraviticThruster(4, 10, 0, 4, 1));
   
	$this->addAftSystem(new GraviticShield(0, 6, 0, 2, 180, 240));
	$this->addAftSystem(new GraviticShield(0, 6, 0, 2, 120, 180));
        $this->addAftSystem(new GraviticThruster(4, 12, 0, 5, 2));
        $this->addAftSystem(new GraviticThruster(4, 12, 0, 5, 2));
	$this->addAftSystem(new GraviticThruster(4, 12, 0, 5, 2));


	$this->addLeftSystem(new GravitonPulsar(3, 5, 2, 180, 0));
	$this->addLeftSystem(new GravitonPulsar(3, 5, 2, 180, 0));
	$this->addLeftSystem(new GravitonPulsar(3, 5, 2, 180, 0));
	$this->addLeftSystem(new HeavyLaser(4, 8, 6, 240, 0));
	$this->addLeftSystem(new GraviticShield(0, 6, 0, 2, 240, 300));
	$this->addLeftSystem(new Hangar(5, 12));
        $this->addLeftSystem(new GraviticThruster(5, 20, 0, 8, 3));

        
	$this->addRightSystem(new GravitonPulsar(3, 5, 2, 0, 180));
	$this->addRightSystem(new GravitonPulsar(3, 5, 2, 0, 180));
	$this->addRightSystem(new GravitonPulsar(3, 5, 2, 0, 180));
	$this->addRightSystem(new HeavyLaser(4, 8, 6, 0, 120));
	$this->addRightSystem(new GraviticShield(0, 6, 0, 2, 60, 120));
	$this->addRightSystem(new Hangar(5, 12));
        $this->addRightSystem(new GraviticThruster(5, 20, 0, 8, 4));

        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(5, 50));
        $this->addAftSystem(new Structure(5, 48));
        $this->addLeftSystem(new Structure(5, 56));
        $this->addRightSystem(new Structure(5, 56));
        $this->addPrimarySystem(new Structure(5, 48));
		
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
					6 => "Gravitic Shield",
					8 => "Graviton Pulsar",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Gravitic Shield",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					3 => "Thruster",
					5 => "Gravitic Shield",
					7 => "Heavy Laser",
					10 => "Graviton Pulsar",
					12 => "Hangar",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					3 => "Thruster",
					5 => "Gravitic Shield",
					7 => "Heavy Laser",
					10 => "Graviton Pulsar",
					12 => "Hangar",
					18 => "Structure",
					20 => "Primary",
			),
		);
				
    }
}
