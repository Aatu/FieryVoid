<?php
class BrixadiiWarshipPrototype extends BaseShipNoAft{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 470;
		$this->faction = "ZNexus Brixadii";
        $this->phpclass = "BrixadiiWarshipPrototype";
        $this->imagePath = "img/ships/Nexus/BrixadiiWarship.png";
			$this->canvasSize = 145; //img has 200px per side
        $this->shipClass = "Warship Prototype";
			$this->variantOf = "Warship";
			$this->occurence = "common";		
			$this->limited = 10;
		$this->unofficial = true;
		$this->isd = 2053;

	    $this->notes .= '<br>Crude Jump Drive';
		
        $this->forwardDefense = 14;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
		$this->iniativebonus = 10;
        
        $this->addPrimarySystem(new Reactor(5, 20, 0, 0));
        $this->addPrimarySystem(new CnC(5, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 5, 5));
        $this->addPrimarySystem(new Engine(4, 18, 0, 10, 4));
		$this->addPrimarySystem(new Hangar(2, 4));
		$this->addPrimarySystem(new JumpEngine(4, 12, 5, 40));
		$this->addPrimarySystem(new Thruster(4, 15, 0, 5, 2));
		$this->addPrimarySystem(new Thruster(4, 15, 0, 5, 2));

        $this->addFrontSystem(new Thruster(3, 14, 0, 5, 1));
        $this->addFrontSystem(new Thruster(3, 14, 0, 5, 1));
		$this->addFrontSystem(new NexusProjectorArray(3, 6, 1, 240, 60));
		$this->addFrontSystem(new NexusProjectorArray(3, 6, 1, 300, 120));
		$this->addFrontSystem(new HvyParticleProjector(3, 8, 4, 300, 60));
        
		$this->addLeftSystem(new Thruster(3, 7, 0, 4, 3));
		$this->addLeftSystem(new Thruster(3, 7, 0, 4, 3));
		$this->addLeftSystem(new NexusProjectorArray(3, 6, 1, 180, 360));
		$this->addLeftSystem(new NexusChaffLauncher(1, 2, 1, 180, 360));
		$this->addLeftSystem(new HvyParticleProjector(3, 8, 4, 240, 360));

		$this->addRightSystem(new Thruster(3, 7, 0, 4, 4));
		$this->addRightSystem(new Thruster(3, 7, 0, 4, 4));
		$this->addRightSystem(new NexusProjectorArray(3, 6, 1, 0, 180));
		$this->addRightSystem(new NexusChaffLauncher(1, 2, 1, 0, 180));
		$this->addRightSystem(new HvyParticleProjector(3, 8, 4, 0, 120));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 45));
        $this->addLeftSystem(new Structure( 4, 50));
        $this->addRightSystem(new Structure( 4, 50));
        $this->addPrimarySystem(new Structure( 5, 40));
		
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
                    10 => "Jump Engine",
                    12 => "Thruster",
					14 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    6 => "Thruster",
					8 => "Heavy Particle Projector",
					10 => "Projector Array",
					18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    5 => "Thruster",
					7 => "Heavy Particle Projector",
					9 => "Projector Array",
					10 => "Chaff Launcher",
                    18 => "Structure",
                    20 => "Primary",
			),
            4=> array(
                    5 => "Thruster",
					7 => "Heavy Particle Projector",
					9 => "Projector Array",
					10 => "Chaff Launcher",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );

		
    }
}
?>