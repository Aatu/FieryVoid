<?php
class TrekFederationKelvin extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 425;
        $this->faction = "ZStarTrek Federation (TOS)";
        $this->phpclass = "TrekFederationKelvin";
        $this->imagePath = "img/ships/StarTrek/FederationKelvin.png";
        $this->shipClass = "Kelvin Destroyer";

	$this->unofficial = true;
        $this->canvasSize = 100;
	$this->isd = 2230;

	$this->fighters = array("Shuttlecraft"=>20);
		$this->customFighter = array("Human small craft"=>20); //can deploy small craft with Human crew
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->gravitic = true;  
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 1;
        $this->pivotcost = 3;
        $this->iniativebonus = 8 *5;  //intentionally lowered

        $this->addPrimarySystem(new Reactor(4, 20, 0, 2));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 5, 5));
        $this->addPrimarySystem(new Hangar(4, 20,20));
	$impulseDrive = new TrekImpulseDrive(4,24,0,4,3); //Impulse Drive is an engine in its own right, in addition to serving as hub for Nacelle output: $armour, $maxhealth, $powerReq, $output, $boostEfficiency

	$projection = new TrekShieldProjection(2, 20, 6, 270, 90, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(2, 6, 2, 2, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addFrontSystem($projector);
		$projector = new TrekShieldProjector(2, 6, 2, 2, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addFrontSystem($projector);
	$this->addFrontSystem($projection);

	$this->addFrontSystem(new TrekLightPhaserLance(3, 0, 0, 240, 60));
	$this->addFrontSystem(new TrekLightPhaserLance(3, 0, 0, 270, 90));
	$this->addFrontSystem(new TrekLightPhaserLance(3, 0, 0, 300, 120));
	$this->addFrontSystem(new TrekPhasedPulseCannon(2, 0, 0, 240, 60));
	$this->addFrontSystem(new TrekPhasedPulseCannon(2, 0, 0, 300, 120));


	    
		$warpNacelle = new TrekWarpDrive(4, 40, 0, 6); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);


	$projection = new TrekShieldProjection(2, 20, 6, 90, 270, 'A');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(2, 6, 2, 2, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addAftSystem($projector);
		$projector = new TrekShieldProjector(2, 6, 2, 2, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addAftSystem($projector);
	$this->addAftSystem($projection);

        $this->addAftSystem(new TrekPhasedPulseCannon(2, 0, 0, 90, 270));
	$this->addAftSystem(new TrekLightPhaserLance(3, 0, 0, 90, 270));


		//technical thrusters - unlimited, like for LCVs		
		$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 4)); //unhitable and with unlimited thrust allowance   
        $this->addPrimarySystem($impulseDrive);

        $this->addPrimarySystem(new Structure(4, 54));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			4 => "2:Nacelle",
			9 => "Hangar",
			12 => "Scanner",
			15 => "Engine",
			18 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			2 => "Shield Projector",
		    6 => "Light Phaser Lance",
			9 => "Phased Pulse Cannon",
			10 => "0:Hangar",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
		    4 => "Nacelle",
			6 => "0:Hangar",	
			8 => "Shield Projector",
		    10 => "Light Phaser Lance",
			12 => "Phased Pulse Cannon",
			18 => "Structure",		
			20 => "Primary",
		),

	);

        
        }
    }
?>