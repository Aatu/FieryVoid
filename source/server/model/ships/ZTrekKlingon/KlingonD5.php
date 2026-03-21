<?php
class KlingonD5 extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 475;
        $this->faction = "ZStarTrek Klingon";
        $this->phpclass = "KlingonD5";
        $this->imagePath = "img/ships/StarTrek/KlingonD5Cruiser.png";
        $this->shipClass = "Klingon D5 Cruiser";

		$this->unofficial = true;
        $this->canvasSize = 100;
	    $this->isd = 2147;

		$this->fighters = array("Shuttlecraft"=>2);
		$this->customFighter = array("Klingon small craft"=>2); //can deploy small craft with Klingon crew
        
        $this->forwardDefense = 13;
        $this->sideDefense = 13;
        
        $this->gravitic = true;  
        $this->turncost = 0.5;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus =  13 *5;

		$this->trueStealth = true;
		$this->canPreOrder = true;

		$this->addPrimarySystem(new CloakingDevice(4, 10, 4, 0));
        $this->addPrimarySystem(new Reactor(4, 18, 0, 0));
        $this->addPrimarySystem(new CnC(4, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 5, 5));
        $this->addPrimarySystem(new Hangar(4, 2));
		$this->addPrimarySystem(new MicroJumpSystem(4, 8, 0, 330, 30, 6, 4));   // Armor, health, power, start arc, end arc, distance, reload time 

		$impulseDrive = new TrekImpulseDrive(4,20,0,3,3); //Impulse Drive is an engine in its own right, in addition to serving as hub for Nacelle output: $armour, $maxhealth, $powerReq, $output, $boostEfficiency

		$projection = new TrekShieldProjection(2, 15, 5, 270, 90, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(2, 4, 1, 2, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addFrontSystem($projector);
			$projector = new TrekShieldProjector(2, 4, 1, 2, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addFrontSystem($projector);
		$this->addFrontSystem($projection);

		$this->addFrontSystem(new TrekDisruptorCannon(4, 8, 6, 300, 60));
		$this->addFrontSystem(new TrekPhotonicTorp(2, 6, 1, 270, 90));
		$this->addFrontSystem(new TrekPhotonicTorp(2, 6, 1, 270, 90));
		$this->addFrontSystem(new TrekEarlyDisruptor(3, 6, 4, 0, 360));
		$this->addFrontSystem(new TrekEarlyDisruptor(3, 6, 4, 0, 360));
	    
		$warpNacelle = new TrekWarpDrive(3, 20, 0, 3); //armor, structure, power usage, impulse output
			$impulseDrive->addThruster($warpNacelle);
			$this->addAftSystem($warpNacelle);
		$warpNacelle = new TrekWarpDrive(3, 20, 0, 3); //armor, structure, power usage, impulse output
			$impulseDrive->addThruster($warpNacelle);
			$this->addAftSystem($warpNacelle);

		$this->addAftSystem(new TrekLightDisruptorArray(3, 6, 3, 120, 360));
		$this->addAftSystem(new TrekLightDisruptorArray(3, 6, 3, 0, 240));

		$projection = new TrekShieldProjection(1, 12, 3, 90, 270, 'A');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(1, 4, 1, 2, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addAftSystem($projector);
		$this->addAftSystem($projection);
		
		//technical thrusters - unlimited, like for LCVs		
		$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 4)); //unhitable and with unlimited thrust allowance   
        $this->addPrimarySystem($impulseDrive);

        $this->addPrimarySystem(new Structure(4, 50));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			3 => "2:Nacelle",
			6 => "Structure",
           	8 => "Cloaking Device",
			11 => "Micro Jump System",
			12 => "Hangar",		
			14 => "Scanner",
			16 => "Engine",
			18 => "Reactor",
			20 => "C&C",
		),

		1=> array(
		    2 => "Shield Projector",
			6 => "Photonic Torpedo",
			8 => "Disruptor Cannon",
			11 => "Early Disruptor",
			18 => "Structure",
			20 => "Primary",
		),

		2=> array(
		    2 => "Shield Projector",
			7 => "Nacelle",
			9 => "Light Disruptor Array",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }

?>