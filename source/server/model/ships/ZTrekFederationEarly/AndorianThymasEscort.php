<?php
class AndorianThymasEscort extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 375;
        $this->faction = "ZStarTrek Federation (early)";
        $this->phpclass = "AndorianThymasEscort";
        $this->imagePath = "img/ships/StarTrek/AndorianThyzon.png";
        $this->shipClass = "Andorian Thymas Escort Frigate";

	$this->unofficial = true;
        $this->canvasSize = 100;
	$this->isd = 2150;
	    $this->occurence = "uncommon";
	    $this->variantOf = "Andorian Thyzon Frigate";

	$this->fighters = array("Shuttlecraft"=>1);
		$this->customFighter = array("Andorian small craft"=>1); //can deploy small craft with Andorian crew
        
        $this->forwardDefense = 11;
        $this->sideDefense = 14;
        
        $this->agile = true;
        $this->gravitic = true;  
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus =  13 *5; 

        $this->addPrimarySystem(new Reactor(3, 12, 0, 0));
        $this->addPrimarySystem(new CnC(3, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 3, 4));
        $this->addPrimarySystem(new Hangar(3, 1));
	$this->addPrimarySystem(new SWTractorBeam(2,0,360,1));
	$impulseDrive = new TrekImpulseDrive(3,16,0,1,3); //Impulse Drive is an engine in its own right, in addition to serving as hub for Nacelle output: $armour, $maxhealth, $powerReq, $output, $boostEfficiency


		$projection = new TrekShieldProjection(2, 12, 5, 270, 90, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(2, 6, 1, 2, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addFrontSystem($projector);
			$projector = new TrekShieldProjector(2, 6, 1, 2, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addFrontSystem($projector);
		$this->addFrontSystem($projection);
        	$this->addFrontSystem(new LightParticleCannon(2, 6, 4, 270, 90));
        	$this->addFrontSystem(new StdParticleBeam(2, 4, 2, 270, 90));

        	$this->addAftSystem(new StdParticleBeam(2, 4, 2, 180, 360));
        	$this->addAftSystem(new StdParticleBeam(2, 4, 2, 180, 360));
        	$this->addAftSystem(new StdParticleBeam(2, 4, 2, 0, 180));
        	$this->addAftSystem(new StdParticleBeam(2, 4, 2, 0, 180));
		$warpNacelle = new TrekWarpDrive(3, 9, 0, 2); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
		$warpNacelle = new TrekWarpDrive(3, 9, 0, 2); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
		$warpNacelle = new TrekWarpDrive(3, 9, 0, 2); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
		$warpNacelle = new TrekWarpDrive(3, 9, 0, 2); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);

		$projection = new TrekShieldProjection(1, 9, 4, 90, 270, 'A');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(1, 4, 1, 1, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addAftSystem($projector);
			$projector = new TrekShieldProjector(1, 4, 1, 1, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addAftSystem($projector);
		$this->addAftSystem($projection);

		
		//technical thrusters - unlimited, like for LCVs		
		$this->addFrontSystem(new InvulnerableThruster(99, 1, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 1, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 4)); //unhitable and with unlimited thrust allowance   
        $this->addPrimarySystem($impulseDrive);

        $this->addPrimarySystem(new Structure(3, 40));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			5 => "2:Nacelle",			
			8 => "Tractor Beam",
			11 => "Scanner",
			12 => "Hangar",
			15 => "Engine",
			18 => "Reactor",
			20 => "C&C",
		),

		1=> array(
		    	2 => "Shield Projector",
			6 => "Light Particle Cannon",
			7 => "Standard Particle Beam",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
		    	2 => "Shield Projector",
			9 => "Nacelle",
			13 => "Standard Particle Beam",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>