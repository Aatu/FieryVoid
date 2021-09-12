<?php
class DeltaClassBeta extends LCV{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 210;
        $this->faction = "ZTrek Playtest Federation";
        $this->phpclass = "DeltaClassBeta";
        $this->imagePath = "img/ships/StarTrek/DeltaClass.png";
        $this->shipClass = "Delta Beta";

		$this->unofficial = true;
        $this->canvasSize = 65;
		$this->isd = 2150;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 11;

		$this->variantOf = "Delta Alpha";

        $this->gravitic = true;    
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 70;


		$this->addFrontSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance

		$this->addPrimarySystem(new Reactor(3, 10, 0, 2));
		$this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
    	$sensors = new Scanner(3, 12, 4, 3);
			$sensors->markLCV();
			$this->addPrimarySystem($sensors);
		$this->addPrimarySystem(new Engine(2, 14, 0, 6, 1));

		$polarizedhullplating = new AbsorbtionShield(2,4,3,1,0,360);  //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
			$polarizedhullplating->displayName = "Polarized Hull Plating";
			$this->addPrimarySystem($polarizedhullplating);
		$this->addPrimarySystem(new TrekPhaseCannon(3, 6, 4, 270, 90));
		$this->addPrimarySystem(new TrekPhaseCannon(3, 6, 4, 90, 270));
		$this->addPrimarySystem(new TrekWarpDrive(2, 8, 2, 16));
		$this->addPrimarySystem(new TrekWarpDrive(2, 8, 2, 16));

        $this->addPrimarySystem(new Structure(3, 30));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			10 => "Structure",
			12 => "Phase Cannon",
			13 => "Polarized Hull Plating",
			16 => "Warp Drive",
			18 => "Engine",
			19 => "Reactor",
			20 => "Scanner",
		),

		1=> array(
			10 => "0:Structure",
			13 => "0:Phase Cannon",
			14 => "0:Polarized Hull Plating",
			16 => "0:Warp Drive",
			18 => "0:Engine",
			19 => "0:Reactor",
			20 => "0:Scanner",
		),

		2=> array(
			9 => "Structure",
			10 => "0:Phase Cannon",
			11 => "0:Polarized Hull Plating",
			16 => "0:Warp Drive",
			18 => "0:Engine",
			19 => "0:Reactor",
			20 => "0:Scanner",
		),

	);

        
        }
    }
?>