<?php
class TrekFederationDeltaFFGamma extends LCV{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 220;
        $this->faction = "ZStarTrek (early) Federation";
        $this->phpclass = "TrekFederationDeltaFFGamma";
        $this->imagePath = "img/ships/StarTrek/DeltaClass.png";
        $this->shipClass = "Delta Frigate Gamma";
	$this->variantOf = "Delta Frigate Alpha";

	$this->unofficial = true;
        $this->canvasSize = 100;
	$this->isd = 2170;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 11;

		$this->notes = "Does not require hangar.";

        $this->gravitic = true;    
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 14 *5; //LCV standard
		$this->hangarRequired = ''; //no hangar required!



	$this->addPrimarySystem(new Reactor(3, 10, 0, 0));
	$this->addPrimarySystem(new CnC(99, 1, 0, 0)); //C&C should be unhittable anyway
	    	$sensors = new Scanner(3, 12, 4, 3);
		//$sensors->markLCV();//regular Sensors
		$this->addPrimarySystem($sensors);
	$impulseDrive = new TrekImpulseDrive(3,11,0,1,2);


	$projection = new TrekShieldProjection(1, 8, 3, 0, 360, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(2, 6, 2, 2, 0, 360, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addPrimarySystem($projector);
	$this->addPrimarySystem($projection);
		
		$this->addFrontSystem(new TrekPhaseCannon(3, 0, 0, 240, 30));
		$this->addFrontSystem(new TrekPhaseCannon(3, 0, 0, 330, 120));

		$this->addAftSystem(new TrekLtPhaseCannon(2, 0, 0, 120, 360));
		$this->addAftSystem(new TrekLtPhaseCannon(2, 0, 0, 0, 240));


		$warpNacelle = new TrekWarpDrive(2, 10, 0, 3); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
		$warpNacelle = new TrekWarpDrive(2, 10, 0, 3); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);


	$this->addFrontSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance
        $this->addPrimarySystem($impulseDrive);

        $this->addPrimarySystem(new Structure(3, 30));

	//d20 hit chart
	$this->hitChart = array( //differences are deliberate
		
		0=> array(
			10 => "Structure",
		    12 => "0:Shield Projector",
			14 => "1:Phase Cannon",
			16 => "2:Light Phase Cannon",
			17 => "2:Nacelle",
			18 => "0:Engine",
			19 => "0:Reactor",
			20 => "0:Scanner",
		),

		1=> array(
			11 => "Structure",
		    13 => "0:Shield Projector",
			15 => "1:Phase Cannon",
			16 => "2:Light Phase Cannon",
			17 => "2:Nacelle",
			18 => "0:Engine",
			19 => "0:Reactor",
			20 => "0:Scanner",
		),

		2=> array(
			10 => "Structure",
		    12 => "0:Shield Projector",
			13 => "1:Phase Cannon",
			15 => "2:Light Phase Cannon",
			17 => "2:Nacelle",
			18 => "0:Engine",
			19 => "0:Reactor",
			20 => "0:Scanner",
		),

	);

        
        }
    }
?>