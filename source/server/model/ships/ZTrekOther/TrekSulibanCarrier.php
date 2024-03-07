<?php
class TrekSulibanCarrier extends HeavyCombatVessel{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 450;
        $this->faction = "ZTrek Playtest Other Factions";
        $this->phpclass = "TrekSulibanCarrier";
        $this->imagePath = "img/ships/StarTrek/SulibanCarrier.png";
        $this->shipClass = "Suliban Carrier";

	$this->unofficial = true;
	$this->isd = 2145;
        $this->fighters = array("normal"=>24);
        
        $this->forwardDefense = 13;
        $this->sideDefense = 16;
        
        $this->gravitic = true;  
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 1;
        $this->pivotcost = 3;
        $this->iniativebonus =  5 *5; 

        $this->addPrimarySystem(new Reactor(4, 20, 0, 2));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 14, 4, 5));
	$lightgun = new SWMediumLaser(2, 180, 60, 4);
	$lightgun->displayName = "Defense Guns";
	$this->addPrimarySystem($lightgun);
	$lightgun = new SWMediumLaser(2, 300, 180, 4);
	$lightgun->displayName = "Defense Guns";
	$this->addPrimarySystem($lightgun);
	$impulseDrive = new TrekImpulseDrive(4,20,0,2,2); //Impulse Drive is an engine in its own right, in addition to serving as hub for Nacelle output: $armour, $maxhealth, $powerReq, $output, $boostEfficiency

        $this->addFrontSystem(new ParticleCutter(4, 8, 3, 300, 60));
	$lightgun = new SWMediumLaser(2, 180, 60, 4);
	$lightgun->displayName = "Defense Guns";
	$this->addFrontSystem($lightgun);
	$lightgun = new SWMediumLaser(2, 300, 180, 4);
	$lightgun->displayName = "Defense Guns";
	$this->addFrontSystem($lightgun);
        $this->addFrontSystem(new Hangar(3, 6));
        $this->addFrontSystem(new Hangar(3, 6));

		$projection = new TrekShieldProjection(0, 20, 6, 270, 90, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(1, 8, 2, 4, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addFrontSystem($projector);
			$projector = new TrekShieldProjector(1, 8, 2, 4, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addFrontSystem($projector);
		$this->addFrontSystem($projection);

		$warpNacelle = new TrekWarpDrive(3, 30, 0, 6); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);


	$lightgun = new SWMediumLaser(2, 120, 360, 4);
	$lightgun->displayName = "Defense Guns";
	$this->addAftSystem($lightgun);
	$lightgun = new SWMediumLaser(2, 0, 240, 4);
	$lightgun->displayName = "Defense Guns";
	$this->addAftSystem($lightgun);
        $this->addAftSystem(new Hangar(3, 6));
        $this->addAftSystem(new Hangar(3, 6));

		$projection = new TrekShieldProjection(0, 18, 6, 90, 270, 'A');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(1, 8, 2, 4, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addAftSystem($projector);
			$projector = new TrekShieldProjector(1, 8, 2, 4, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addAftSystem($projector);
		$this->addAftSystem($projection);

		
		//technical thrusters - unlimited, like for LCVs		
		$this->addFrontSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance   
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance
        $this->addPrimarySystem($impulseDrive);

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 40));
        $this->addAftSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 4, 40));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			4 => "2:Nacelle",
			7 => "Defense Guns",			
			11 => "Scanner",
			15 => "Engine",
			18 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			4 => "Particle Cutter",
			6 => "Defense Guns",
		    	8 => "Shield Projector",
			11 => "Hangar",	
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
		    	2 => "Shield Projector",
			4 => "Defense Guns",
			7 => "Hangar",	
			11 => "Nacelle",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>