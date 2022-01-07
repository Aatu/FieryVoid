<?php
class VulcanLander extends LCV{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 180;
        $this->faction = "ZTrek Federation (early)";
        $this->phpclass = "VulcanLander";
        $this->imagePath = "img/ships/StarTrek/VulcanLander.png";
        $this->shipClass = "Vulcan T'Plana-Hath Lander";
		
		$this->notes = 'No hangar required.'; 

	$this->unofficial = true;
        $this->canvasSize = 100;
	$this->isd = 2040;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;

        $this->gravitic = true;    
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 70;
		$this->hangarRequired = ''; //no hangar required!


		$this->addFrontSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance

		$this->addPrimarySystem(new Reactor(3, 10, 0, 2));
		$this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
    	$sensors = new Scanner(3, 10, 4, 2);
			$sensors->markLCV();
			$this->addPrimarySystem($sensors);
	$impulseDrive = new TrekImpulseDrive(3,16,0,0,3);

	$projection = new TrekShieldProjection(1, 12, 4, 0, 360, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(2, 6, 2, 2, 0, 360, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addPrimarySystem($projector);
	$this->addPrimarySystem($projection);
		$this->addFrontSystem(new TrekLtPhaseCannon(2, 4, 2, 240, 120));
		$this->addAftSystem(new TrekLtPhaseCannon(2, 4, 2, 120, 360));
		$this->addAftSystem(new TrekLtPhaseCannon(2, 4, 2, 0, 240));

		$warpNacelle = new TrekWarpDrive(2, 10, 0, 2); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addFrontSystem($warpNacelle);
		$warpNacelle = new TrekWarpDrive(2, 10, 0, 2); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addFrontSystem($warpNacelle);
		$warpNacelle = new TrekWarpDrive(2, 10, 0, 2); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
        $this->addPrimarySystem($impulseDrive);

        $this->addPrimarySystem(new Structure(3, 24));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			7 => "Structure",
			9 => "1:Light Phase Cannon",
			12 => "2:Light Phase Cannon",
			13 => "Shield Projector",
			16 => "1:Nacelle",
			17 => "2:Nacelle",
			18 => "Engine",
			19 => "Reactor",
			20 => "Scanner",
		),

		1=> array(
			8 => "Structure",
			10 => "Light Phase Cannon",
			11 => "0:Shield Projector",
			17 => "Nacelle",
			18 => "0:Engine",
			19 => "0:Reactor",
			20 => "0:Scanner",
		),

		2=> array(
			8 => "Structure",
			12 => "Light Phase Cannon",
			13 => "0:Shield Projector",
			17 => "Nacelle",
			18 => "0:Engine",
			19 => "0:Reactor",
			20 => "0:Scanner",
		),

	);

        
        }
    }
?>