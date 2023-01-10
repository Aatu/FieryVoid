<?php
class VulcanShran extends BaseShipNoAft{
	
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 485;
	$this->faction = "ZStarTrek Federation (early)";
        $this->phpclass = "VulcanShran";
        $this->imagePath = "img/ships/StarTrek/VulcanShran.png";
        $this->shipClass = "Vulcan Sh'Ran Cruiser";

	$this->unofficial = true;
	    $this->isd = '2140';


	$this->fighters = array("Shuttlecraft"=>4);
		$this->customFighter = array("Vulcan small craft"=>4); //can deploy small craft with Vulcan crew
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
 
        $this->gravitic = true;       
        $this->turncost = 0.66;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 4;
	$this->iniativebonus = 0; 
		
	$this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new Reactor(4, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 5, 5));
	$this->addPrimarySystem(new Hangar(3, 4, 4));
	$this->addPrimarySystem(new SWTractorBeam(2,0,360,1)); 

	$impulseDrive = new TrekImpulseDrive(4,20,0,2,3); //Impulse Drive is an engine in its own right, in addition to serving as hub for Nacelle output: $armour, $maxhealth, $powerReq, $output, $boostEfficiency
		
  
	$projection = new TrekShieldProjection(2, 20, 6, 300, 60, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(1, 6, 2, 2, 300, 60, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addFrontSystem($projector);
		$projector = new TrekShieldProjector(1, 6, 2, 2, 300, 60, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addFrontSystem($projector);
	$this->addFrontSystem($projection);
	$this->addFrontSystem(new TrekHvyPhaseCannon(3, 8, 6, 300, 60));
	$this->addFrontSystem(new TrekPhaseCannon(3, 6, 4, 240, 60));
	$this->addFrontSystem(new TrekPhaseCannon(3, 6, 4, 300, 120));

	$projection = new TrekShieldProjection(2, 20, 6, 180, 300, 'L');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(1, 6, 2, 2, 180, 300, 'L'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addLeftSystem($projector);
		$projector = new TrekShieldProjector(1, 6, 2, 2, 180, 300, 'L'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addLeftSystem($projector);
	$this->addLeftSystem($projection);
	$this->addLeftSystem(new TrekPhotonicTorp(2, 6, 1, 240, 0));
	$this->addLeftSystem(new TrekLtPhaseCannon(2, 4, 2, 180, 360));
	$this->addLeftSystem(new TrekPhaseCannon(3, 6, 4, 120, 300));
	$warpNacelle = new TrekWarpDrive(3, 18, 0, 4); //armor, structure, power usage, impulse output
	$impulseDrive->addThruster($warpNacelle);
	$this->addLeftSystem($warpNacelle);


	$projection = new TrekShieldProjection(2, 20, 6, 60, 180, 'R');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(1, 6, 2, 2, 60, 180, 'R'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addRightSystem($projector);
		$projector = new TrekShieldProjector(1, 6, 2, 2, 60, 180, 'R'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addRightSystem($projector);
	$this->addRightSystem($projection);
	$this->addRightSystem(new TrekPhotonicTorp(2, 6, 1, 0, 120));
	$this->addRightSystem(new TrekLtPhaseCannon(2, 4, 2, 0, 180));
	$this->addRightSystem(new TrekPhaseCannon(3, 6, 4, 60, 240));
	$warpNacelle = new TrekWarpDrive(3, 18, 0, 4); //armor, structure, power usage, impulse output
	$impulseDrive->addThruster($warpNacelle);
	$this->addRightSystem($warpNacelle);



		
	//technical thrusters - unlimited, like for LCVs		
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance  
        $this->addPrimarySystem($impulseDrive);
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 36));
        $this->addLeftSystem(new Structure( 3, 36));
        $this->addRightSystem(new Structure( 3, 36));
        $this->addPrimarySystem(new Structure( 3, 36));

	    
        $this->hitChart = array(
            0=> array(
				8 => "Structure",
				10 => "Tractor Beam",
				12 => "Scanner",
				15 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
            ),
            1=> array(
				2 => "Shield Projector",
				5 => "Heavy Phase Cannon",
				9 => "Phase Cannon",
				18 => "Structure",
				20 => "Primary",
            ),
            3=> array(
				2 => "Shield Projector",
				6 => "Nacelle",
				8 => "Photonic Torpedo",
				9 => "Light Phase Cannon",
				11 => "Phase Cannon",
				18 => "Structure",
				20 => "Primary",
            ),
            4=> array(
				2 => "Shield Projector",
				6 => "Nacelle",
				8 => "Photonic Torpedo",
				9 => "Light Phase Cannon",
				11 => "Phase Cannon",
				18 => "Structure",
				20 => "Primary",
            ),
       );
	    
	    
    }
}
?>
