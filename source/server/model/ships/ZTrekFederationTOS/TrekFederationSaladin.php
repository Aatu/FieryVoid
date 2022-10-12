<?php
class TrekFederationSaladin extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 350;
        $this->faction = "ZTrek Playtest Federation (TOS)";
        $this->phpclass = "TrekFederationSaladin";
        $this->imagePath = "img/ships/StarTrek/FederationSaladin.png";
        $this->shipClass = "Saladin Escort Destroyer";

	$this->unofficial = true;
        $this->canvasSize = 100;
	$this->isd = 2263;

	$this->fighters = array("Shuttlecraft"=>1);
		$this->customFighter = array("Human small craft"=>1); //can deploy small craft with Human crew
        
        $this->forwardDefense = 11;
        $this->sideDefense = 12;
 
        //$this->agile = true; //NOT agile after all       
        $this->gravitic = true;  
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 1;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 13 *5;

        $this->addPrimarySystem(new Reactor(4, 12, 0, 0));
        $this->addPrimarySystem(new CnC(4, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 5, 5));
        $this->addPrimarySystem(new Hangar(3, 1, 1));
	$impulseDrive = new TrekImpulseDrive(3,18,0,3,3); //Impulse Drive is an engine in its own right, in addition to serving as hub for Nacelle output: $armour, $maxhealth, $powerReq, $output, $boostEfficiency

	$projection = new TrekShieldProjection(2, 12, 6, 270, 90, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(2, 6, 2, 2, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addFrontSystem($projector);
		$projector = new TrekShieldProjector(2, 6, 2, 2, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addFrontSystem($projector);
	$this->addFrontSystem($projection);

	$this->addFrontSystem(new SWTractorBeam(3,0,360,1));
	$this->addFrontSystem(new TrekPhaser(3, 0, 0, 300, 60));
	$this->addFrontSystem(new TrekLightPhaserLance(3, 0, 0, 240, 90));
	$this->addFrontSystem(new TrekLightPhaserLance(3, 0, 0, 270, 120));

	    
		$warpNacelle = new TrekWarpDrive(3, 24, 0, 5); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);

	$projection = new TrekShieldProjection(2, 12, 6, 90, 270, 'A');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(2, 6, 2, 2, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addAftSystem($projector);
		$projector = new TrekShieldProjector(2, 6, 2, 2, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addAftSystem($projector);
	$this->addAftSystem($projection);

	$this->addAftSystem(new TrekLightPhaserLance(3, 0, 0, 120, 360));
	$this->addAftSystem(new TrekLightPhaserLance(3, 0, 0, 0, 240));
		
		//technical thrusters - unlimited, like for LCVs		
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance   
        $this->addPrimarySystem($impulseDrive);

        $this->addPrimarySystem(new Structure(4, 36));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			6 => "2:Nacelle",
			9 => "Scanner",
			12 => "Hangar",
			15 => "Engine",
			18 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			2 => "Shield Projector",
		    	4 => "Phaser",
			8 => "Light Phaser Lance",
			10 => "Tractor Beam",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
		    	5 => "Nacelle",
			7 => "Shield Projector",
			10 => "Light Phaser Lance",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>