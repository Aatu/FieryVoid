<?php
class TrekFederationFreedomFF extends MediumShip{
	
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 150;
        $this->faction = "ZStarTrek (early) Federation";
        $this->phpclass = "TrekFederationFreedomFF";
        $this->imagePath = "img/ships/StarTrek/FederationFreedomFF.png";
        $this->shipClass = "Freedom Frigate (Early)";

	$this->unofficial = true;
        $this->canvasSize = 100;
	$this->isd = 2148;

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

        $this->addPrimarySystem(new Reactor(3, 6, 0, 0));
        $this->addPrimarySystem(new CnC(3, 5, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 7, 3, 2));
	$impulseDrive = new TrekImpulseDrive(3,14,0,2,2);
        $this->addPrimarySystem(new Hangar(2, 1));

		$projection = new TrekShieldProjection(1, 4, 3, 270, 90, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->displayName = "Polarized Hull Plating";
		$this->addFrontSystem($projection);
		
		$pulsecannon = new ScatterPulsar(1, 4, 2, 180, 60);//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		//$pulsecannon->displayName = "Pulse Cannon";
		$this->addFrontSystem($pulsecannon);
		$pulsecannon = new ScatterPulsar(1, 4, 2, 240, 120);//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		//$pulsecannon->displayName = "Pulse Cannon";
		$this->addFrontSystem($pulsecannon);
		$pulsecannon = new ScatterPulsar(1, 4, 2, 300, 180);//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		//$pulsecannon->displayName = "Pulse Cannon";
		$this->addFrontSystem($pulsecannon);
      		$this->addFrontSystem(new TrekSpatialTorp(2, 6, 1, 300, 60));

	    
		$warpNacelle = new TrekWarpDrive(3, 16, 0, 4); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
		$warpNacelle = new TrekWarpDrive(3, 16, 0, 4); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
	
		$projection = new TrekShieldProjection(1, 4, 3, 90, 270, 'A');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->displayName = "Polarized Hull Plating";
		$this->addAftSystem($projection);


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
			2 => "2:Nacelle",
			9 => "Hangar",
			12 => "Scanner",
			15 => "Engine",
			18 => "Reactor",
			20 => "C&C",
		),


		1=> array(
		    4 => "Scatter Pulsar",
		    6 => "Spatial Torpedo",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
		    6 => "Nacelle",
			17 => "Structure",
			20 => "Primary",
		),
	);

        
        }
    }
?>