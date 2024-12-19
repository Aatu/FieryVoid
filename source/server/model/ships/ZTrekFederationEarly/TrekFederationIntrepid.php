<?php
class TrekFederationIntrepid extends MediumShip{
	
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 180;
        $this->faction = "ZStarTrek Federation (early)";
        $this->phpclass = "TrekFederationIntrepid";
        $this->imagePath = "img/ships/StarTrek/Intrepid.png";
        $this->shipClass = "Intrepid";

	$this->unofficial = true;
        $this->canvasSize = 100;
	$this->isd = 2150;

	$this->fighters = array("Shuttlecraft"=>2);
		$this->customFighter = array("Human small craft"=>2); //can deploy small craft with Human crew
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;

        $this->gravitic = true;    
        $this->turncost = 0.5;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 11 *5; //deliberately lowered compared to standard MCV

        $this->addPrimarySystem(new Reactor(3, 8, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 3, 3));
	$impulseDrive = new TrekImpulseDrive(3,15,0,1,3);
        $this->addPrimarySystem(new Hangar(3, 2));
/*
		$polarizedhullplating = new AbsorbtionShield(2,4,3,1,270,90);  //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
		$polarizedhullplating->displayName = "Polarized Hull Plating";
		$this->addFrontSystem($polarizedhullplating);
	*/	
		$projection = new TrekShieldProjection(1, 6, 3, 270, 90, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->displayName = "Polarized Hull Plating";
		$this->addFrontSystem($projection);
		
		$this->addFrontSystem(new TrekPhaseCannon(3, 0, 0, 300, 60));
      		$this->addFrontSystem(new TrekSpatialTorp(2, 0, 0, 300, 60));
        	$this->addFrontSystem(new TrekSpatialTorp(2, 0, 0, 300, 60));
	    
		$warpNacelle = new TrekWarpDrive(3, 12, 0, 4); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
		$warpNacelle = new TrekWarpDrive(3, 12, 0, 4); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
		/*
		$polarizedhullplating = new AbsorbtionShield(2,4,3,1,90,270);  //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
			$polarizedhullplating->displayName = "Polarized Hull Plating";
			$this->addAftSystem($polarizedhullplating);
			*/			
		$projection = new TrekShieldProjection(1, 6, 3, 90, 270, 'A');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->displayName = "Polarized Hull Plating";
		$this->addAftSystem($projection);
		
        	$this->addAftSystem(new TrekSpatialTorp(2, 0, 0, 120, 240));

		//technical thrusters - unlimited, like for LCVs		
		$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 4)); //unhitable and with unlimited thrust allowance   
        $this->addPrimarySystem($impulseDrive);

        $this->addPrimarySystem(new Structure(3, 45));

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
			8 => "Spatial Torpedo",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
		    7 => "Nacelle",
			10 => "Spatial Torpedo",
			17 => "Structure",
			20 => "Primary",
		),
	);

        
        }
    }
?>