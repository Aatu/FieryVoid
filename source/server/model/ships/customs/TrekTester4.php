<?php
class TrekTester4 extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 800;
        $this->faction = "Custom Ships";
        $this->phpclass = "TrekTester4";
        $this->imagePath = "img/ships/StarTrek/EnterpriseNX.png";
        $this->shipClass = "Trek Testbed Capital";

		$this->unofficial = true;
        $this->canvasSize = 100;
	    $this->isd = 2150;
        
        $this->forwardDefense = 16;
        $this->sideDefense = 17;

        $this->gravitic = true;    
        $this->turncost = 0.75;
        $this->turndelaycost = 0.75;
        $this->accelcost = 3;
        $this->rollcost = 4;
        $this->pivotcost = 4;
        $this->iniativebonus = 0;
		
		

        $this->addPrimarySystem(new Reactor(6, 30, 0, 1));
        $this->addPrimarySystem(new CnC(6, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 25, 6, 10));
        $this->addPrimarySystem(new Hangar(5, 4));
		$grappler = new CustomIndustrialGrappler(2, 5, 0, 0, 360);
			$grappler->displayName = "Magnetic Grappler";
			$this->addPrimarySystem($grappler);
			
			
		$projection = new TrekShieldProjection(3, 45, 8, 330, 30, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(2, 6, 3, 5, 330, 30, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addFrontSystem($projector);
			$projector = new TrekShieldProjector(2, 6, 3, 5, 330, 30, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addFrontSystem($projector);
		$this->addFrontSystem($projection);
			
		$this->addFrontSystem(new TrekPhaseCannon(3, 6, 4, 270, 60));
		$this->addFrontSystem(new TrekPhaseCannon(3, 6, 4, 300, 90));    
        $this->addFrontSystem(new HeavyLaser(4, 8, 6, 300, 60));   
   		$this->addFrontSystem(new TrekPhotonicTorp(2, 6, 1, 300, 60));
       	$this->addFrontSystem(new TrekPhotonicTorp(2, 6, 1, 300, 60));
	    	
		
		$impulseDrive = new TrekImpulseDrive(4,30,0,3,4); //Impulse Drive is an engine in its own right, in addition to serving as hub for Nacelle output: $armour, $maxhealth, $powerReq, $output, $boostEfficiency
			$warpNacelle = new TrekWarpDrive(4, 26, 0, 5); //armor, structure, power usage, impulse output
			$impulseDrive->addThruster($warpNacelle);
			$this->addAftSystem($warpNacelle);		
			$warpNacelle = new TrekWarpDrive(4, 26, 0, 5); //armor, structure, power usage, impulse output
			$impulseDrive->addThruster($warpNacelle);
			$this->addAftSystem($warpNacelle);
        $this->addPrimarySystem($impulseDrive);
		

		$projection = new TrekShieldProjection(3, 40, 8, 150, 210, 'A');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(2, 6, 2, 4, 150, 210, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addAftSystem($projector);
		$this->addAftSystem($projection);
		
		$this->addAftSystem(new TrekPhaseCannon(2, 6, 4, 120, 270));
		$this->addAftSystem(new TrekPhaseCannon(2, 6, 4, 90, 240));
   		$this->addAftSystem(new TrekPhotonicTorp(2, 6, 1, 300, 60));  //make it forward firing :)
   		$this->addAftSystem(new TrekPhotonicTorp(2, 6, 1, 300, 60));  //make it forward firing :)
        $this->addAftSystem(new HeavyLaser(4, 8, 6, 120, 240));    



		$projection = new TrekShieldProjection(3, 45, 8, 210, 330, 'L');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(2, 6, 3, 5, 210, 330, 'L'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addLeftSystem($projector);
			$projector = new TrekShieldProjector(2, 6, 3, 5, 210, 330, 'L'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addLeftSystem($projector);
		$this->addLeftSystem($projection);

		$this->addLeftSystem(new TrekPhaseCannon(3, 6, 4, 210, 30));
		$this->addLeftSystem(new TrekPhaseCannon(3, 6, 4, 210, 30));
        $this->addLeftSystem(new HeavyLaser(4, 8, 6, 240, 0));    



		$projection = new TrekShieldProjection(3, 45, 8, 30, 150, 'R');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(2, 6, 3, 5, 30, 150, 'R'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addRightSystem($projector);
			$projector = new TrekShieldProjector(2, 6, 3, 5, 30, 150, 'R'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addRightSystem($projector);
		$this->addRightSystem($projection);
		
		$this->addRightSystem(new TrekPhaseCannon(3, 6, 4, 330, 150));
		$this->addRightSystem(new TrekPhaseCannon(3, 6, 4, 330, 150));
        $this->addRightSystem(new HeavyLaser(4, 8, 6, 0, 120));    



		//technical thrusters - unlimited, like for LCVs		
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance  

        $this->addPrimarySystem(new Structure(6, 55));
        $this->addFrontSystem(new Structure(5, 50));
        $this->addAftSystem(new Structure(4, 45));
        $this->addLeftSystem(new Structure(5, 55));
        $this->addRightSystem(new Structure(5, 55));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			10 => "Structure",
			11 => "Magnetic Grappler",
			14 => "Scanner",
			15 => "Hangar",
			17 => "Engine",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
		    1 => "Shield Projector",
			4 => "Phase Cannon",
			7 => "Photonic Torpedo",
			9 => "Heavy Laser",
			18 => "Structure",
			20 => "Primary",
		),

		2=> array(
		    1 => "Shield Projector",
			4 => "Phase Cannon",
			7 => "Photonic Torpedo",
			9 => "Heavy Laser",
			18 => "Structure",
			20 => "Primary",
		),
		
		3=> array(
		    1 => "Shield Projector",
			5 => "Phase Cannon",
			8 => "Heavy Laser",
			18 => "Structure",
			20 => "Primary",
		),
		
		4=> array(
		    1 => "Shield Projector",
			5 => "Phase Cannon",
			8 => "Heavy Laser",
			18 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
