<?php
class VulcanTimur extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 380;
        $this->faction = "ZTrek Playtest Vulcans";
        $this->phpclass = "VulcanTimur";
        $this->imagePath = "img/ships/StarTrek/VulcanSurak.png";
        $this->shipClass = "Ti'Mur Science Vessel";
		$this->occurence = "uncommon";
		$this->variantOf = "Surak Light Cruiser";

	$this->unofficial = true;
        $this->canvasSize = 200;
	$this->isd = 2142;

	$this->fighters = array("Shuttlecraft"=>3);
        
        $this->forwardDefense = 12;
        $this->sideDefense = 14;
        
        $this->gravitic = true;  
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus =  10 *5; //deliberately lowered compared to standard MCV

        $this->addPrimarySystem(new Reactor(3, 11, 0, 0));
        $this->addPrimarySystem(new CnC(3, 9, 0, 0));
        $this->addPrimarySystem(new ELINTScanner(3, 10, 7, 7));
        $this->addPrimarySystem(new Hangar(3, 3, 3));
	$this->addPrimarySystem(new SWTractorBeam(2,0,360,1));
	$impulseDrive = new TrekImpulseDrive(3,20,0,1,3); //Impulse Drive is an engine in its own right, in addition to serving as hub for Nacelle output: $armour, $maxhealth, $powerReq, $output, $boostEfficiency


		$projection = new TrekShieldProjection(2, 12, 5, 270, 90, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(0, 4, 1, 2, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addFrontSystem($projector);
			$projector = new TrekShieldProjector(0, 4, 1, 2, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addFrontSystem($projector);
		$this->addFrontSystem($projection);
		$this->addFrontSystem(new TrekPhaseCannon(3, 6, 4, 270, 90));
		$this->addFrontSystem(new CargoBay(2, 12));
		$this->addFrontSystem(new CargoBay(2, 12);
	    
		$warpNacelle = new TrekWarpDrive(3, 12, 3, 3); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
		$warpNacelle = new TrekWarpDrive(3, 12, 3, 3); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
		$warpNacelle = new TrekWarpDrive(3, 12, 3, 3); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
		$this->addAftSystem(new TrekPhaseCannon(2, 6, 4, 90, 270));


		$projection = new TrekShieldProjection(2, 12, 5, 90, 270, 'A');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(0, 4, 1, 2, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addAftSystem($projector);
			$projector = new TrekShieldProjector(0, 4, 1, 2, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addAftSystem($projector);
		$this->addAftSystem($projection);
		
		//technical thrusters - unlimited, like for LCVs		
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance   
        $this->addPrimarySystem($impulseDrive);

        $this->addPrimarySystem(new Structure(3, 60));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			6 => "Tractor Beam",
			9 => "Elint Scanner",
			12 => "Hangar",
			15 => "Engine",
			18 => "Reactor",
			20 => "C&C",
		),

		1=> array(
		    	2 => "Shield Projector",
			5 => "Phase Cannon",
			9 => "Cargo Bay",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
		    	2 => "Shield Projector",
			9 => "Nacelle",
			11 => "Phase Cannon",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
