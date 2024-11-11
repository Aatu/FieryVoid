<?php
class TrekFederationNXWarCruiserUpg extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 400;
        $this->faction = "ZStarTrek Federation (early)";
        $this->phpclass = "TrekFederationNXWarCruiserUpg";
        $this->imagePath = "img/ships/StarTrek/EnterpriseNX.png";
        $this->shipClass = "NX War Cruiser (upgraded)";
			$this->occurence = "common";
			$this->variantOf = "NX War Cruiser";

		$this->unofficial = true;
        $this->canvasSize = 100;
	    $this->isd = 2166;

	$this->fighters = array("Shuttlecraft"=>2);
		$this->customFighter = array("Human small craft"=>2); //can deploy small craft with Human crew
        
        $this->forwardDefense = 13;
        $this->sideDefense = 13;
        
        $this->gravitic = true;  
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus =  10 *5; //deliberately lowered compared to standard MCV

        $this->addPrimarySystem(new Reactor(3, 15, 0, 4));
        $this->addPrimarySystem(new CnC(3, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 5, 5));
        $this->addPrimarySystem(new Hangar(3, 2));
		$grappler = new CustomIndustrialGrappler(2, 5, 0, 0, 360);
			$grappler->displayName = "Magnetic Grappler";
			$this->addPrimarySystem($grappler);
	$impulseDrive = new TrekImpulseDrive(3,20,0,1,3); //Impulse Drive is an engine in its own right, in addition to serving as hub for Nacelle output: $armour, $maxhealth, $powerReq, $output, $boostEfficiency


		$projection = new TrekShieldProjection(1, 12, 3, 270, 90, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(0, 4, 1, 2, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addFrontSystem($projector);
			$projector = new TrekShieldProjector(0, 4, 1, 2, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addFrontSystem($projector);
		$this->addFrontSystem($projection);

		$this->addFrontSystem(new TrekPhaseCannon(3, 6, 4, 240, 60));
		$this->addFrontSystem(new TrekPhaseCannon(3, 6, 4, 270, 90));
		$this->addFrontSystem(new TrekPhaseCannon(3, 6, 4, 300, 120));
		$this->addFrontSystem(new TrekPhotonicTorp(2, 6, 1, 270, 90));
		$this->addFrontSystem(new TrekPhotonicTorp(2, 6, 1, 270, 90));
	    
		$warpNacelle = new TrekWarpDrive(3, 18, 0, 4); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
		$warpNacelle = new TrekWarpDrive(3, 18, 0, 4); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);

		$this->addAftSystem(new TrekPhaseCannon(2, 6, 4, 90, 270));
		$this->addAftSystem(new TrekPhotonicTorp(2, 6, 1, 120, 240));

		$projection = new TrekShieldProjection(1, 12, 3, 90, 270, 'A');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(0, 4, 1, 2, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addAftSystem($projector);
		$this->addAftSystem($projection);
		
		//technical thrusters - unlimited, like for LCVs		
		$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 4)); //unhitable and with unlimited thrust allowance   
        $this->addPrimarySystem($impulseDrive);

        $this->addPrimarySystem(new Structure(3, 60));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			6 => "Magnetic Grappler",
			9 => "Scanner",
			12 => "Hangar",
			15 => "Engine",
			18 => "Reactor",
			20 => "C&C",
		),

		1=> array(
		    	2 => "Shield Projector",
			7 => "Phase Cannon",
			10 => "Photonic Torpedo",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
		    	2 => "Shield Projector",
			8 => "Nacelle",
			10 => "Phase Cannon",
			12 => "Photonic Torpedo",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
