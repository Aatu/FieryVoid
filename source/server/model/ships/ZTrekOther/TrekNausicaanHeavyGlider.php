<?php
class TrekNausicaanHeavyGlider extends LCV{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 110;
        $this->faction = "ZTrek Playtest Other Factions";
        $this->phpclass = "TrekNausicaanHeavyGlider";
        $this->imagePath = "img/ships/StarTrek/NausicaanHeavyGlider.png";
        $this->shipClass = "Nausicaan Heavy Glider";
		
	$this->notes = "Does not require hangar.";

	$this->unofficial = true;
        $this->canvasSize = 100;
	$this->isd = 2140;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 11;

        $this->gravitic = true;    
        $this->turncost = 0.25;
        $this->turndelaycost = 0.25;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 14 *5;
		$this->hangarRequired = ''; //no hangar required!


		$this->addFrontSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance

		$this->addPrimarySystem(new Reactor(3, 10, 0, 2));
		$this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
    	$sensors = new Scanner(3, 12, 4, 2);
			//$sensors->markLCV();
			$this->addPrimarySystem($sensors);
	$impulseDrive = new TrekImpulseDrive(3,12,0,0,2);

	$projection = new TrekShieldProjection(1, 6, 3, 0, 360, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(2, 6, 2, 1, 0, 360, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addPrimarySystem($projector);
	$this->addPrimarySystem($projection);
		$this->addFrontSystem(new TrekPlasmaBurst(2, 4, 2, 240, 360));
		$this->addFrontSystem(new LtPlasmaProjector(2, 6, 3, 240, 120));
		$this->addFrontSystem(new TrekPlasmaBurst(2, 4, 2, 0, 120));
	

		$warpNacelle = new TrekWarpDrive(2, 10, 0, 3); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
		$warpNacelle = new TrekWarpDrive(2, 10, 0, 3); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
        $this->addPrimarySystem($impulseDrive);

        $this->addPrimarySystem(new Structure(3, 30));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			8 => "Structure",
			10 => "1:Light Plasma Projector",
			12 => "1:Plasma Burst",
			14 => "0:Shield Projector",
			17 => "2:Nacelle",
			18 => "0:Engine",
			19 => "0:Reactor",
			20 => "0:Scanner",
		),

		1=> array(
			8 => "Structure",
			10 => "1:Light Plasma Projector",
			12 => "1:Plasma Burst",
			14 => "0:Shield Projector",
			17 => "2:Nacelle",
			18 => "0:Engine",
			19 => "0:Reactor",
			20 => "0:Scanner",
		),

		2=> array(
			8 => "Structure",
			10 => "1:Light Plasma Projector",
			12 => "1:Plasma Burst",
			14 => "0:Shield Projector",
			17 => "2:Nacelle",
			18 => "0:Engine",
			19 => "0:Reactor",
			20 => "0:Scanner",
		),		

	);

        
        }
    }
?>