<?php
class TrekFederationFreedomUpgrFF extends MediumShip{
	
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 300;
        $this->faction = "ZStarTrek Federation (early)";
        $this->phpclass = "TrekFederationFreedomUpgrFF";
        $this->imagePath = "img/ships/StarTrek/FederationFreedomFF.png";
        $this->shipClass = "Freedom Frigate Upgraded";
        $this->variantOf = "Freedom Frigate (Early)";

	$this->unofficial = true;
        $this->canvasSize = 100;
	$this->isd = 2168;

	$this->fighters = array("Shuttlecraft"=>1);
		$this->customFighter = array("Human small craft"=>1); //can deploy small craft with Human crew
        
        $this->forwardDefense = 11;
        $this->sideDefense = 12;

        $this->gravitic = true;    
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 12 *5; //would be higher on modern vessel

        $this->addPrimarySystem(new Reactor(3, 11, 0, 0));
        $this->addPrimarySystem(new CnC(3, 5, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 9, 4, 4));
	$impulseDrive = new TrekImpulseDrive(3,14,0,3,2);
        $this->addPrimarySystem(new Hangar(2, 1));

		$projection = new TrekShieldProjection(1, 9, 3, 270, 90, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(0, 4, 1, 1, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addFrontSystem($projector);
			$projector = new TrekShieldProjector(0, 4, 1, 1, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addFrontSystem($projector);
		$this->addFrontSystem($projection);
		
		$this->addFrontSystem(new TrekPhotonicTorp(2, 0, 0, 270, 90));	
		$this->addFrontSystem(new TrekPhasedPulseCannon(2, 0, 0, 240, 60));
		$this->addFrontSystem(new TrekPhasedPulseCannon(3, 0, 0, 270, 90));
		$this->addFrontSystem(new TrekPhasedPulseCannon(2, 0, 0, 300, 120));

	    
		$warpNacelle = new TrekWarpDrive(3, 16, 0, 4); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
		$warpNacelle = new TrekWarpDrive(3, 16, 0, 4); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
	
		$projection = new TrekShieldProjection(1, 9, 3, 90, 270, 'A');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(0, 4, 1, 1, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
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
			6 => "Scanner",
			8 => "Hangar",
			14 => "Engine",
			17 => "Reactor",
			20 => "C&C",
		),


		1=> array(
			2 => "Shield Projector",
			7 => "Phased Pulse Cannon",
			9 => "Photonic Torpedo",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			2 => "Shield Projector",
			4 => "Phased Pulse Cannon",
			10 => "Nacelle",
			17 => "Structure",
			20 => "Primary",
		),
	);

        
        }
    }
?>