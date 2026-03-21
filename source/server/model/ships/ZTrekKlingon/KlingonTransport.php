<?php
class KlingonTransport extends LCV{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 225;
        $this->faction = "ZStarTrek Klingon";
        $this->phpclass = "KlingonTransport";
        $this->imagePath = "img/ships/StarTrek/KlingonTransport.png";
        $this->shipClass = "Klingon Transport";

		$this->unofficial = true;
        $this->canvasSize = 65;
		$this->isd = 2138;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 11;
        
        $this->gravitic = true;  
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus =  14 *5;

		$this->trueStealth = true;
		$this->canPreOrder = true;

		$this->addPrimarySystem(new CloakingDevice(3, 6, 4, 0));
        $this->addPrimarySystem(new Reactor(3, 18, 0, 0));
		$this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
        $this->addPrimarySystem(new Scanner(3, 9, 4, 3));
		$this->addPrimarySystem(new MicroJumpSystem(2, 6, 0, 330, 30, 4, 4));   // Armor, health, power, start arc, end arc, distance, reload time 

		$impulseDrive = new TrekImpulseDrive(3,12,0,2,2); //Impulse Drive is an engine in its own right, in addition to serving as hub for Nacelle output: $armour, $maxhealth, $powerReq, $output, $boostEfficiency

		$projection = new TrekShieldProjection(1, 12, 4, 0, 360, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(2, 6, 2, 2, 0, 360, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addPrimarySystem($projector);
		$this->addPrimarySystem($projection);

		$this->addFrontSystem(new TrekPhotonicTorp(2, 6, 1, 270, 90));
		$this->addFrontSystem(new TrekLightDisruptor(3, 0, 0, 240, 60));
		$this->addFrontSystem(new TrekLightDisruptor(3, 0, 0, 300, 120));
	    
		$warpNacelle = new TrekWarpDrive(3, 9, 0, 2); //armor, structure, power usage, impulse output
			$impulseDrive->addThruster($warpNacelle);
			$this->addAftSystem($warpNacelle);
		$warpNacelle = new TrekWarpDrive(3, 9, 0, 2); //armor, structure, power usage, impulse output
			$impulseDrive->addThruster($warpNacelle);
			$this->addAftSystem($warpNacelle);
		
		//technical thrusters - unlimited, like for LCVs		
		$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 4)); //unhitable and with unlimited thrust allowance   
        $this->addPrimarySystem($impulseDrive);

        $this->addPrimarySystem(new Structure(3, 30));

	//d20 hit chart
	$this->hitChart = array(
	
		0=> array(
			9 => "Structure",
			10 => "0:Shield Projector",
            11 => "0:Cloaking Device",
			12 => "0:Micro Jump System",
			17 => "2:Nacelle",
			18 => "0:Engine",
			19 => "0:Reactor",
			20 => "0:Scanner",
		),

		1=> array(
			9 => "Structure",
			10 => "0:Shield Projector",
            11 => "0:Cloaking Device",
			12 => "0:Micro Jump System",
			17 => "2:Nacelle",
			18 => "0:Engine",
			19 => "0:Reactor",
			20 => "0:Scanner",
		),

		2=> array(
			9 => "Structure",
			10 => "0:Shield Projector",
            11 => "0:Cloaking Device",
			12 => "0:Micro Jump System",
			17 => "2:Nacelle",
			18 => "0:Engine",
			19 => "0:Reactor",
			20 => "0:Scanner",
		),

	);

        
        }
    }

?>