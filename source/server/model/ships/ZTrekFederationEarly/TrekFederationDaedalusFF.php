<?php
class TrekFederationDaedalusFF extends MediumShip{
	
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 225;
        $this->faction = "ZStarTrek (early) Federation";
        $this->phpclass = "TrekFederationDaedalusFF";
        $this->imagePath = "img/ships/StarTrek/FederationDaedalusFF.png";
        $this->shipClass = "Daedalus Early Frigate";

	$this->unofficial = true;
        $this->canvasSize = 100;
	$this->isd = 2152;

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
        $this->iniativebonus = 10 *5; //deliberately lowered compared to standard MCV

        $this->addPrimarySystem(new Reactor(3, 8, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 3, 3));
	$impulseDrive = new TrekImpulseDrive(3,15,0,2,3);
        $this->addPrimarySystem(new Hangar(3, 4, 4));
/*
		$polarizedhullplating = new AbsorbtionShield(2,4,3,1,270,90);  //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
		$polarizedhullplating->displayName = "Polarized Hull Plating";
		$this->addFrontSystem($polarizedhullplating);
	*/	
		$projection = new TrekShieldProjection(1, 10, 3, 270, 90, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->displayName = "Polarized Hull Plating";
		$this->addFrontSystem($projection);
		
		$this->addFrontSystem(new TrekPhaseCannon(2, 6, 4, 270, 60));
		$this->addFrontSystem(new TrekPhaseCannon(2, 6, 4, 300, 90));
      		$this->addFrontSystem(new TrekSpatialTorp(2, 6, 1, 300, 60));
        	$this->addFrontSystem(new TrekSpatialTorp(2, 6, 1, 300, 60));
	    
		$warpNacelle = new TrekWarpDrive(2, 14, 0, 3); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
		$warpNacelle = new TrekWarpDrive(2, 14, 0, 3); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
		/*
		$polarizedhullplating = new AbsorbtionShield(2,4,3,1,90,270);  //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
			$polarizedhullplating->displayName = "Polarized Hull Plating";
			$this->addAftSystem($polarizedhullplating);
			*/			
		$projection = new TrekShieldProjection(1, 10, 3, 90, 270, 'A');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->displayName = "Polarized Hull Plating";
		$this->addAftSystem($projection);
		
		$this->addAftSystem(new TrekLtPhaseCannon(2, 4, 2, 90, 270));
		$this->addAftSystem(new TrekLtPhaseCannon(2, 4, 2, 90, 270));

		//technical thrusters - unlimited, like for LCVs		
		$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 4)); //unhitable and with unlimited thrust allowance   
        $this->addPrimarySystem($impulseDrive);

        $this->addPrimarySystem(new Structure(3, 36));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			2 => "2:Nacelle",
			9 => "Structure",
			12 => "Hangar",			
			14 => "Scanner",
			16 => "Engine",
			18 => "Reactor",
			20 => "C&C",
		),

		1=> array(
		    3 => "Phase Cannon",
		    6 => "Spatial Torpedo",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
		    5 => "Nacelle",
			8 => "Light Phase Cannon",
			17 => "Structure",
			20 => "Primary",
		),
	);

        
        }
    }
?>