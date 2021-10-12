<?php
class VulcanVahklas extends LCV{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 325;
        $this->faction = "ZTrek Playtest Federation";
        $this->phpclass = "VulcanVahklas";
        $this->imagePath = "img/ships/StarTrek/VulcanVahklas.png";
        $this->shipClass = "Vulcan Vah'Klas Frigate";
		

	$this->unofficial = true;
        $this->canvasSize = 100;
	$this->isd = 2140;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;

        $this->gravitic = true;    
        $this->turncost = 0.33;
        $this->turndelaycost = 0.25;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 70;


		$this->addFrontSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance

		$this->addPrimarySystem(new Reactor(3, 10, 0, 2));
		$this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
    	$sensors = new Scanner(3, 12, 4, 4);
			$sensors->markLCV();
			$this->addPrimarySystem($sensors);
	$impulseDrive = new TrekImpulseDrive(3,16,0,2,3);

	$projection = new TrekShieldProjection(2, 12, 5, 0, 360, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(2, 6, 2, 2, 0, 360, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addPrimarySystem($projector);
	$this->addPrimarySystem($projection);
		$this->addPrimarySystem(new TrekPhaseCannon(2, 6, 4, 300, 60));
		$this->addPrimarySystem(new TrekPhaseCannon(2, 6, 4, 300, 60));
		$this->addPrimarySystem(new TrekLtPhaseCannon(2, 4, 2, 330, 180));
		$this->addPrimarySystem(new TrekLtPhaseCannon(2, 4, 2, 180, 30));
	

		$warpNacelle = new TrekWarpDrive(3, 12, 3, 4); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addPrimarySystem($warpNacelle);
        $this->addPrimarySystem($impulseDrive);

        $this->addPrimarySystem(new Structure(3, 40));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			8 => "Structure",
			10 => "Phase Cannon",
			12 => "Light Phase Cannon",
			13 => "Shield Projector",
			16 => "Nacelle",
			18 => "Engine",
			19 => "Reactor",
			20 => "Scanner",
		),

		1=> array(
			8 => "Structure",
			10 => "0:Cargo Bay",
			12 => "0:Phase Cannon",
			13 => "0:Shield Projector",
			16 => "0:Nacelle",
			18 => "0:Engine",
			19 => "0:Reactor",
			20 => "0:Scanner",
		),

		2=> array(
			8 => "Structure",
			10 => "0:Cargo Bay",
			12 => "0:Phase Cannon",
			13 => "0:Shield Projector",
			16 => "0:Nacelle",
			18 => "0:Engine",
			19 => "0:Reactor",
			20 => "0:Scanner",
		),

	);

        
        }
    }
?>