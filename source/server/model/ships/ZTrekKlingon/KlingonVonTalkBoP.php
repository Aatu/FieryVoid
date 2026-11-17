<?php
class KlingonVonTalkBoP extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 350;
        $this->faction = "StarTrek Klingon";
        $this->phpclass = "KlingonVonTalkBoP";
        $this->imagePath = "img/ships/StarTrek/KlingonVonTalkBirdofPrey.png";
        $this->shipClass = "Klingon Von'Talk Bird of Prey";

		$this->unofficial = true;
        $this->canvasSize = 100;
	    $this->isd = 2149;

		$this->fighters = array("Shuttlecraft"=>1);
		$this->customFighter = array("Klingon small craft"=>1); //can deploy small craft with Klingon crew
        
        $this->forwardDefense = 11;
        $this->sideDefense = 12;
        
        $this->gravitic = true;  
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus =  14 *5;

		$this->trueStealth = true;
		$this->canPreOrder = true;

		$this->addPrimarySystem(new CloakingDevice(3, 8, 2, 0));
        $this->addPrimarySystem(new Reactor(3, 12, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 4, 4));
        $this->addPrimarySystem(new Hangar(3, 1));
		$this->addPrimarySystem(new MicroJumpSystem(3, 6, 0, 355, 5, 4, 4));   // Armor, health, power, start arc, end arc, distance, reload time 

		$impulseDrive = new TrekImpulseDrive(3,14,0,1,2); //Impulse Drive is an engine in its own right, in addition to serving as hub for Nacelle output: $armour, $maxhealth, $powerReq, $output, $boostEfficiency

		$projection = new TrekShieldProjection(2, 12, 4, 270, 90, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(2, 4, 1, 1, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addFrontSystem($projector);
		$projector = new TrekShieldProjector(2, 4, 1, 1, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addFrontSystem($projector);
			$this->addFrontSystem($projection);

		$this->addFrontSystem(new TrekPhotonicTorp(2, 6, 1, 330, 30));
		$this->addFrontSystem(new TrekEarlyLightDisruptor(1, 4, 2, 270, 360));
		$this->addFrontSystem(new TrekEarlyLightDisruptor(1, 4, 2, 0, 90));
		$this->addFrontSystem(new TrekEarlyMedDisruptor(2, 6, 4, 270, 90));
		$this->addFrontSystem(new TrekEarlyMedDisruptor(2, 6, 4, 270, 90));
	    
		$warpNacelle = new TrekWarpDrive(2, 15, 0, 4); //armor, structure, power usage, impulse output
			$impulseDrive->addThruster($warpNacelle);
			$this->addAftSystem($warpNacelle);
		$warpNacelle = new TrekWarpDrive(2, 15, 0, 4); //armor, structure, power usage, impulse output
			$impulseDrive->addThruster($warpNacelle);
			$this->addAftSystem($warpNacelle);
		$this->addAftSystem(new TrekEarlyDisruptor(2, 6, 4, 300, 60));
		$this->addAftSystem(new TrekEarlyDisruptor(2, 6, 4, 300, 60));

		$projection = new TrekShieldProjection(1, 8, 3, 90, 270, 'A');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(1, 4, 1, 1, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addAftSystem($projector);
		$this->addAftSystem($projection);
		
		//technical thrusters - unlimited, like for LCVs		
		$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 4)); //unhitable and with unlimited thrust allowance   
        $this->addPrimarySystem($impulseDrive);

        $this->addPrimarySystem(new Structure(3, 33));

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
			4 => "Photonic Torpedo",
			7 => "Early Light Disruptor",
			10 => "Early Medium Disruptor",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
		    2 => "Shield Projector",
			7 => "Nacelle",
			9 => "Early Disruptor",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>