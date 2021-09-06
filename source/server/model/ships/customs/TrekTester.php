<?php
class TrekTester extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 325;
        $this->faction = "Custom Ships";
        $this->phpclass = "TrekTester";
        $this->imagePath = "img/ships/StarTrek/EnterpriseNX.png";
        $this->shipClass = "Trek Testbed";

		$this->unofficial = true;
        $this->canvasSize = 100;
	    $this->isd = 2150;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 13;

        $this->gravitic = true;    
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 60;
		
		

        $this->addPrimarySystem(new Reactor(3, 10, 0, 3));
        $this->addPrimarySystem(new CnC(3, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 9, 4, 4));
        $this->addPrimarySystem(new Engine(3, 10, 0, 2, 5));
        $this->addPrimarySystem(new Hangar(3, 2));
		$grappler = new CustomIndustrialGrappler(2, 5, 0, 0, 360);
			$grappler->displayName = "Magnetic Grappler";
			$this->addPrimarySystem($grappler);
			
			
		$impulseDrive = new TrekImpulseDrive(3,20,0,0,3); //Impulse Drive is an engine in its own right, in addition to serving as hub for Nacelle output: $armour, $maxhealth, $powerReq, $output, $boostEfficiency
		

		
			

		$polarizedhullplating = new AbsorbtionShield(2,4,3,1,270,90);  //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
			$polarizedhullplating->displayName = "Polarized Hull Plating";
			$this->addFrontSystem($polarizedhullplating);
		$this->addFrontSystem(new TrekPhaseCannon(3, 6, 4, 270, 60));
		$this->addFrontSystem(new TrekPhaseCannon(3, 6, 4, 300, 90));
   		$this->addFrontSystem(new TrekSpatialTorp(2, 6, 1, 300, 60));
       	$this->addFrontSystem(new TrekSpatialTorp(2, 6, 1, 300, 60));
	    
		
		
		$warpNacelle = new TrekWarpDrive(4, 18, 3, 4); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
		
		$warpNacelle = new TrekWarpDrive(4, 18, 3, 4); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
		
		/*
		$this->addAftSystem(new TrekWarpDrive(2, 18, 3, 13));
		$this->addAftSystem(new TrekWarpDrive(2, 18, 3, 13));
		*/
   		//$this->addAftSystem(new Engine(3, 14, 0, 6, 2));
		$this->addAftSystem(new TrekPhaseCannon(2, 6, 4, 90, 270));
   		$this->addAftSystem(new TrekSpatialTorp(2, 6, 1, 120, 240));
		$polarizedhullplating = new AbsorbtionShield(2,4,3,1,90,270);  //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
			$polarizedhullplating->displayName = "Polarized Hull Plating";
			$this->addAftSystem($polarizedhullplating);

		//technical thrusters - unlimited, like for LCVs		
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance  
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
			5 => "Phase Cannon",
			8 => "Spatial Torpedo",
		    9 => "Polarized Hull Plating",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			8 => "Nacelle",
		    9 => "Polarized Hull Plating",
			10 => "Phase Cannon",
			11 => "Spatial Torpedo",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
