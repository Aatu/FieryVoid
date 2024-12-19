<?php
class TrekFederationIntrepidUpgr extends MediumShip{
	
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 275;
        $this->faction = "ZStarTrek Federation (early)";
        $this->phpclass = "TrekFederationIntrepidUpgr";
        $this->imagePath = "img/ships/StarTrek/Intrepid.png";
        $this->shipClass = "Intrepid Upgraded";
		$this->variantOf = "Intrepid";

		$this->unofficial = true;
        $this->canvasSize = 100;
		$this->isd = 2167;

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
        $this->iniativebonus = 12 *5;


//Primary
        $this->addPrimarySystem(new Reactor(3, 12, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 4, 4));
	$impulseDrive = new TrekImpulseDrive(3,15,0,2,2);
        $this->addPrimarySystem(new Hangar(3, 2));

//Front
		$projection = new TrekShieldProjection(1, 10, 3, 270, 90, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(0, 4, 1, 2, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addFrontSystem($projector);
		$this->addFrontSystem($projection);
		
		$this->addFrontSystem(new TrekPhaseCannon(3, 0, 0, 300, 60));
		$this->addFrontSystem(new TrekPhotonicTorp(2, 0, 0, 300, 60));
		$this->addFrontSystem(new TrekPhotonicTorp(2, 0, 0, 300, 60));		
			
	    
//Aft		
		$warpNacelle = new TrekWarpDrive(3, 12, 0, 4); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
		$warpNacelle = new TrekWarpDrive(3, 12, 0, 4); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);

		$projection = new TrekShieldProjection(1, 10, 3, 90, 270, 'A');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(0, 4, 1, 2, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addAftSystem($projector);
		$this->addAftSystem($projection);
		
        	$this->addAftSystem(new TrekPhotonicTorp(2, 0, 0, 120, 240));
		$this->addAftSystem(new TrekLtPhaseCannon(2, 0, 0, 90, 270));

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
			2 => "Shield Projector",
			5 => "Phase Cannon",
			9 => "Photonic Torpedo",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Nacelle",
			8 => "Shield Projector",
			10 => "Photonic Torpedo",
			12 => "Light Phase Cannon",
			17 => "Structure",
			20 => "Primary",
		),
	);

        
        }
    }
?>