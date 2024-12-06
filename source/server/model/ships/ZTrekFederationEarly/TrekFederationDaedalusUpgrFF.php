<?php
class TrekFederationDaedalusUpgrFF extends MediumShip{
	
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 300;
        $this->faction = "ZStarTrek Federation (early)";
        $this->phpclass = "TrekFederationDaedalusUpgrFF";
        $this->imagePath = "img/ships/StarTrek/FederationDaedalusFF.png";
        $this->shipClass = "Daedalus Frigate Upgraded";
			$this->occurence = "common";
			$this->variantOf = "Daedalus Early Frigate";
	$this->unofficial = true;
        $this->canvasSize = 100;
	$this->isd = 2165;

	$this->fighters = array("Shuttlecraft"=>4);
		$this->customFighter = array("Human small craft"=>4); //can deploy small craft with Human crew
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;

        $this->gravitic = true;    
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 12 *5;

        $this->addPrimarySystem(new Reactor(3, 14, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 4, 4));
	$impulseDrive = new TrekImpulseDrive(3,16,0,2,2);
        $this->addPrimarySystem(new Hangar(3, 4, 4));

		$projection = new TrekShieldProjection(1, 10, 3, 270, 90, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(0, 4, 1, 2, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addFrontSystem($projector);
			$projector = new TrekShieldProjector(0, 4, 1, 2, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addFrontSystem($projector);
		$this->addFrontSystem($projection);

		$this->addFrontSystem(new TrekPhaseCannon(2, 0, 0, 270, 60));
		$this->addFrontSystem(new TrekPhaseCannon(2, 0, 0, 300, 90));
		$this->addFrontSystem(new TrekPhotonicTorp(2, 0, 0, 270, 90));
		$this->addFrontSystem(new TrekPhotonicTorp(2, 0, 0, 270, 90));

	    
		$warpNacelle = new TrekWarpDrive(2, 14, 0, 3); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
		$warpNacelle = new TrekWarpDrive(2, 14, 0, 3); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);

		$projection = new TrekShieldProjection(1, 10, 3, 90, 270, 'A');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(0, 4, 1, 2, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addAftSystem($projector);
		$this->addAftSystem($projection);
		
		$this->addAftSystem(new TrekLtPhaseCannon(2, 0, 0, 90, 330));
		$this->addAftSystem(new TrekLtPhaseCannon(2, 0, 0, 30, 270));

		//technical thrusters - unlimited, like for LCVs		
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance   
        $this->addPrimarySystem($impulseDrive);

        $this->addPrimarySystem(new Structure(3, 36));

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
		    4 => "Phase Cannon",
		    8 => "Photonic Torpedo",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
		    7 => "Nacelle",
			10 => "Light Phase Cannon",
			17 => "Structure",
			20 => "Primary",
		),
	);

        
        }
    }
?>