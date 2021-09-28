<?php
class TrekFederationDeltaFFBeta extends LCV{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 200;
        $this->faction = "ZTrek Playtest Federation";
        $this->phpclass = "TrekFederationDeltaFFBeta";
        $this->imagePath = "img/ships/StarTrek/DeltaClass.png";
        $this->shipClass = "Delta Frigate Beta";

	$this->unofficial = true;
        $this->canvasSize = 100;
	$this->isd = 2150;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 11;

	$this->variantOf = "Delta Frigate Alpha";

        $this->gravitic = true;    
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 12 *5; //deliberately lowered compared to standard LCV
		$this->hangarRequired = ''; //no hangar required!



	$this->addPrimarySystem(new Reactor(3, 10, 0, 0));
	$this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
	    	$sensors = new Scanner(3, 12, 4, 3);
		//$sensors->markLCV();//let's give Beta regular Sensors...
		$this->addPrimarySystem($sensors);
	$impulseDrive = new TrekImpulseDrive(3,10,0,0,2);

/*
		$polarizedhullplating = new AbsorbtionShield(2,4,2,1,0,360);  //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
		$polarizedhullplating->displayName = "Polarized Hull Plating";
		$this->addPrimarySystem($polarizedhullplating);
	*/		
		$projection = new TrekShieldProjection(1, 6, 3, 0, 360, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->displayName = "Polarized Hull Plating";
		$this->addPrimarySystem($projection);
		
		$this->addFrontSystem(new TrekPhaseCannon(3, 6, 4, 180, 30));
		$this->addFrontSystem(new TrekPhaseCannon(3, 6, 4, 330, 180));

		$warpNacelle = new TrekWarpDrive(2, 10, 2, 3); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
		$warpNacelle = new TrekWarpDrive(2, 10, 2, 3); //armor, structure, power usage, impulse output
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
			1 => "Polarized Hull Plating",
			10 => "Structure",
			13 => "1:Phase Cannon",
			17 => "2:Nacelle",
			18 => "Engine",
			19 => "Reactor",
			20 => "Scanner",
		),

		1=> array(
			1 => "0:Polarized Hull Plating",
			10 => "Structure",
			14 => "1:Phase Cannon",
			17 => "2:Nacelle",
			18 => "0:Engine",
			19 => "0:Reactor",
			20 => "0:Scanner",
		),

		2=> array(
			1 => "0:Polarized Hull Plating",
			10 => "Structure",
			12 => "1:Phase Cannon",
			17 => "2:Nacelle",
			18 => "0:Engine",
			19 => "0:Reactor",
			20 => "0:Scanner",
		),

	);

        
        }
    }
?>