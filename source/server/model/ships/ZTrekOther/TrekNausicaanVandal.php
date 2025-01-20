<?php
class TrekNausicaanVandal extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 375;
        $this->faction = "ZTrek Playtest Other Factions";
        $this->phpclass = "TrekNausicaanVandal";
        $this->imagePath = "img/ships/StarTrek/NausicaanVandal.png";
        $this->shipClass = "Nausicaan Vandal Destroyer";

	$this->unofficial = true;
        $this->canvasSize = 100;
	$this->isd = 2160;
	$this->fighters = array("light"=>6,"Shuttlecraft"=>1);
		$this->customFighter = array("Nausicaan small craft"=>7, "Nausicaan assault craft"=>1); //can deploy small craft with Nausicaan crew
        
        $this->forwardDefense = 11;
        $this->sideDefense = 15;
        
        $this->gravitic = true;  
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus =  11 *5; 

        $this->addPrimarySystem(new Reactor(3, 14, 0, 0));
        $this->addPrimarySystem(new CnC(3, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 4, 4));
        $this->addPrimarySystem(new Hangar(3, 7));
	$impulseDrive = new TrekImpulseDrive(3,16,0,1,3); //Impulse Drive is an engine in its own right, in addition to serving as hub for Nacelle output: $armour, $maxhealth, $powerReq, $output, $boostEfficiency


		$projection = new TrekShieldProjection(3, 12, 6, 270, 90, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(2, 6, 1, 1, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addFrontSystem($projector);
			$projector = new TrekShieldProjector(2, 6, 1, 1, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addFrontSystem($projector);
		$this->addFrontSystem($projection);
		$this->addFrontSystem(new PlasmaProjector(4, 8, 5, 300, 60));
		$this->addFrontSystem(new EWDualRocketLauncher(3, 6, 2, 270, 90));
		$this->addFrontSystem(new LtPlasmaProjector(2, 6, 3, 240, 120));

		$this->addAftSystem(new LtPlasmaProjector(2, 6, 3, 180, 360));
		$this->addAftSystem(new LtPlasmaProjector(2, 6, 3, 0, 180));
		$this->addAftSystem(new EWDualRocketLauncher(3, 6, 2, 240, 60));
		$this->addAftSystem(new EWDualRocketLauncher(3, 6, 2, 300, 120));
		$warpNacelle = new TrekWarpDrive(3, 15, 0, 4); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
		$warpNacelle = new TrekWarpDrive(3, 15, 0, 4); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);


		$projection = new TrekShieldProjection(2, 12, 5, 90, 270, 'A');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(2, 6, 1, 1, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addAftSystem($projector);
			$projector = new TrekShieldProjector(2, 6, 1, 1, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addAftSystem($projector);
		$this->addAftSystem($projection);

		
		//technical thrusters - unlimited, like for LCVs		
		$this->addFrontSystem(new InvulnerableThruster(99, 1, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 1, 0, 99, 2)); //unhitable and with unlimited thrust allowance   
		$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 4)); //unhitable and with unlimited thrust allowance
        $this->addPrimarySystem($impulseDrive);

        $this->addPrimarySystem(new Structure(3, 40));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			6 => "2:Nacelle",
			9 => "Hangar",			
			12 => "Scanner",
			15 => "Engine",
			18 => "Reactor",
			20 => "C&C",
		),

		1=> array(
		    	2 => "Shield Projector",
			5 => "Plasma Projector",
			7 => "Light Plasma Projector",
			9 => "Dual Rocket Launcher",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
		    	2 => "Shield Projector",
			7 => "Nacelle",
			10 => "Dual Rocket Launcher",
			12 => "Light Plasma Projector",
			18 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>