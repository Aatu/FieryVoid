<?php
class TrekSulibanCruiser extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 425;
        $this->faction = "ZTrek Playtest Other Factions";
        $this->phpclass = "TrekSulibanCruiser";
        $this->imagePath = "img/ships/StarTrek/SulibanCruiser.png";
        $this->shipClass = "Suliban Cruiser";

	$this->unofficial = true;
        $this->canvasSize = 100;
	$this->isd = 2145;
        $this->fighters = array("normal"=>6);
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        
        $this->gravitic = true;  
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus =  10 *5; 

        $this->addPrimarySystem(new Reactor(4, 18, 0, 2));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 4, 5));
        $this->addPrimarySystem(new Hangar(3, 6));
	$impulseDrive = new TrekImpulseDrive(4,20,0,2,2); //Impulse Drive is an engine in its own right, in addition to serving as hub for Nacelle output: $armour, $maxhealth, $powerReq, $output, $boostEfficiency

        $this->addFrontSystem(new ParticleCutter(4, 8, 3, 300, 60));
        $this->addFrontSystem(new ParticleCutter(4, 8, 3, 300, 60));
	$lightgun = new SWMediumLaser(2, 240, 120, 4);
	$lightgun->displayName = "Defense Guns";
	$this->addFrontSystem($lightgun);


		$warpNacelle = new TrekWarpDrive(3, 14, 0, 3); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
		$warpNacelle = new TrekWarpDrive(3, 14, 0, 3); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);

	$lightgun = new SWMediumLaser(2, 120, 360, 4);
	$lightgun->displayName = "Defense Guns";
	$this->addAftSystem($lightgun);
	$lightgun = new SWMediumLaser(2, 0, 240, 4);
	$lightgun->displayName = "Defense Guns";
	$this->addAftSystem($lightgun);

		$projection = new TrekShieldProjection(0, 20, 6, 0, 360, 'A');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(1, 8, 2, 4, 0, 360, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addAftSystem($projector);
			$projector = new TrekShieldProjector(1, 8, 2, 4, 0, 360, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addAftSystem($projector);
		$this->addAftSystem($projection);

		
		//technical thrusters - unlimited, like for LCVs		
		$this->addFrontSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance   
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance
        $this->addPrimarySystem($impulseDrive);

        $this->addPrimarySystem(new Structure(4, 44));

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
			4 => "Particle Cutter",
			6 => "Defense Guns",
		    	7 => "2:Shield Projector",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
		    	2 => "Shield Projector",
			5 => "Defense Guns",
			10 => "Nacelle",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>