<?php
class BrixadiiAssaultShip extends BaseShipNoAft{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 375;
		$this->faction = "ZNexus Brixadii";
        $this->phpclass = "BrixadiiAssaultShip";
        $this->imagePath = "img/ships/Nexus/BrixadiiWarship.png";
			$this->canvasSize = 200; //img has 200px per side
        $this->shipClass = "Assault Ship";
			$this->limited = 33;
		$this->unofficial = true;
		$this->isd = 2116;
         
	    $this->notes = 'Crude Jump Drive';
        $this->fighters = array("assault shuttles" => 4, "superheavy" => 3);
		
		
        $this->forwardDefense = 14;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
		$this->iniativebonus = 0*5;
        
         
        $this->addPrimarySystem(new Reactor(5, 15, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 5, 6));
        $this->addPrimarySystem(new Engine(4, 18, 0, 10, 4));
		$this->addPrimarySystem(new Hangar(2, 6));
		$this->addPrimarySystem(new JumpEngine(4, 12, 5, 40));
		$this->addPrimarySystem(new Thruster(4, 15, 0, 5, 2));
		$this->addPrimarySystem(new Thruster(4, 15, 0, 5, 2));

        $this->addFrontSystem(new Thruster(3, 14, 0, 5, 1));
        $this->addFrontSystem(new Thruster(3, 14, 0, 5, 1));
		$this->addFrontSystem(new NexusProjectorArray(3, 6, 1, 240, 60));
		$this->addFrontSystem(new NexusProjectorArray(3, 6, 1, 300, 120));
		$this->addFrontSystem(new Quarters(4, 9));
		$this->addFrontSystem(new Quarters(4, 9));
		$this->addFrontSystem(new Catapult(2, 6));
        
		$this->addLeftSystem(new Thruster(3, 14, 0, 8, 3));
		$this->addLeftSystem(new NexusProjectorArray(3, 6, 1, 180, 360));
		$this->addLeftSystem(new NexusLightProjectorArray(2, 5, 2, 180, 360));
		$this->addLeftSystem(new NexusChaffLauncher(1, 2, 1, 180, 360));
		$this->addLeftSystem(new Catapult(2, 6));
		$this->addLeftSystem(new CargoBay(2, 9));

		$this->addRightSystem(new Thruster(3, 14, 0, 8, 4));
		$this->addRightSystem(new NexusProjectorArray(3, 6, 1, 0, 180));
		$this->addRightSystem(new NexusLightProjectorArray(2, 5, 2, 0, 180));
		$this->addRightSystem(new NexusChaffLauncher(1, 2, 1, 0, 180));
		$this->addRightSystem(new Catapult(2, 6));
		$this->addRightSystem(new CargoBay(2, 9));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 40));
        $this->addLeftSystem(new Structure( 4, 40));
        $this->addRightSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 5, 44));
		
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
                    10 => "JumpEngine",
                    12 => "Thruster",
					14 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    6 => "Thruster",
					8 => "Catapult",
					10 => "Quarters",
					12 => "Projector Array",
					18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    5 => "Thruster",
					6 => "Chaff Launcher",
					8 => "Catapult",
					10 => "Cargo Bay",
                    11 => "Projector Array",
					12 => "Light Projector Array",
                    18 => "Structure",
                    20 => "Primary",
			),
            4=> array(
                    5 => "Thruster",
					6 => "Chaff Launcher",
					8 => "Catapult",
					10 => "Cargo Bay",
                    11 => "Projector Array",
					12 => "Light Projector Array",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );

		
    }
}
?>