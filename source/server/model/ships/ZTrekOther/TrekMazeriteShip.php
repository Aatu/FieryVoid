<?php
class TrekMazeriteShip extends MediumShip{
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 240;
        $this->faction = "ZTrek Playtest Other Factions";
        $this->phpclass = "TrekMazeriteShip";
        $this->imagePath = "img/ships/StarTrek/MazeriteShip.png";
        $this->shipClass = "Mazerite Ship";

	$this->unofficial = true;
        $this->canvasSize = 100;
	$this->isd = 2145;
	$this->fighters = array("Shuttlecraft"=>2);
		$this->customFighter = array("Mazerite small craft"=>4); //can deploy small craft with appropriate crew
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        
        $this->gravitic = true;  
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus =  11 *5; 

        $this->addPrimarySystem(new Reactor(4, 16, 0, 2));
        $this->addPrimarySystem(new CnC(4, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 4, 4));
        $this->addPrimarySystem(new Hangar(3, 2));
	$impulseDrive = new TrekImpulseDrive(4,18,0,0,2); //Impulse Drive is an engine in its own right, in addition to serving as hub for Nacelle output: $armour, $maxhealth, $powerReq, $output, $boostEfficiency

	$maingun = new SWMediumTLaser(2, 240, 120, 2);
	$maingun->displayName = "Mazerite Particle Gun";
	$this->addFrontSystem($maingun);
	$maingun = new SWMediumTLaser(2, 240, 120, 2);
	$maingun->displayName = "Mazerite Particle Gun";
	$this->addFrontSystem($maingun);
	$lightgun = new SWMediumLaser(2, 240, 120, 3);
	$lightgun->displayName = "Mazerite Light Particle Gun";
	$this->addFrontSystem($lightgun);


		$warpNacelle = new TrekWarpDrive(3, 12, 0, 3); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
		$warpNacelle = new TrekWarpDrive(3, 12, 0, 3); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);

	$lightgun = new SWMediumLaser(2, 120, 360, 3);
	$lightgun->displayName = "Mazerite Light Particle Gun";
	$this->addAftSystem($lightgun);
	$lightgun = new SWMediumLaser(2, 0, 240, 3);
	$lightgun->displayName = "Mazerite Light Particle Gun";
	$this->addAftSystem($lightgun);

		$projection = new TrekShieldProjection(1, 12, 4, 0, 360, 'A');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(1, 4, 1, 1, 0, 360, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addAftSystem($projector);
			$projector = new TrekShieldProjector(1, 4, 1, 1, 0, 360, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addAftSystem($projector);
		$this->addAftSystem($projection);

		
		//technical thrusters - unlimited, like for LCVs		
		$this->addFrontSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance   
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance
        $this->addPrimarySystem($impulseDrive);

        $this->addPrimarySystem(new Structure(4, 33));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			4 => "2:Nacelle",
			7 => "Hangar",			
			11 => "Scanner",
			15 => "Engine",
			18 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			4 => "Mazerite Particle Gun",
			6 => "Mazerite Light Particle Gun",
		    	7 => "2:Shield Projector",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
		    	2 => "Shield Projector",
			5 => "Mazerite Light Particle Gun",
			10 => "Nacelle",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>