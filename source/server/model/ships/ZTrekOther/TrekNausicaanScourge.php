<?php
class TrekNausicaanScourge extends HeavyCombatVessel{
	
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 575;
	$this->faction = "ZTrek Playtest Other Factions";
        $this->phpclass = "TrekNausicaanScourge";
        $this->imagePath = "img/ships/StarTrek/NausicaanScourge.png";
        $this->shipClass = "Nausicaan Scourge Cruiser";

        $this->limited = 33;	
	$this->unofficial = true;
	    $this->isd = '2262';

	$this->fighters = array("heavy"=>12,"Shuttlecraft"=>2);
		$this->customFighter = array("Nausicaan small craft"=>14); //can deploy small craft with Nausicaan crew
        
        $this->forwardDefense = 13;
        $this->sideDefense = 16;
 
        $this->gravitic = true;       
        $this->turncost = 0.66;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 3;
	$this->iniativebonus = 6 *5; 
		
	$this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Reactor(4, 22, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 18, 5, 5));
	$this->addPrimarySystem(new Hangar(3, 14, 6));

	$impulseDrive = new TrekImpulseDrive(3,24,0,3,3); //Impulse Drive is an engine in its own right, in addition to serving as hub for Nacelle output: $armour, $maxhealth, $powerReq, $output, $boostEfficiency
		
  
	$projection = new TrekShieldProjection(3, 24, 7, 270, 90, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(2, 8, 2, 2, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addFrontSystem($projector);
		$projector = new TrekShieldProjector(2, 8, 2, 2, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addFrontSystem($projector);
	$this->addFrontSystem($projection);
		$this->addFrontSystem(new PlasmaProjector(4, 8, 5, 300, 60));
		$this->addFrontSystem(new PlasmaProjector(4, 8, 5, 300, 60));
		$this->addFrontSystem(new LtPlasmaProjector(2, 6, 3, 270, 90));
		$this->addFrontSystem(new EWPointPlasmaGun(2, 3, 2, 240, 90));
		$this->addFrontSystem(new EWPointPlasmaGun(2, 3, 2, 270, 120));

	$warpNacelle = new TrekWarpDrive(3, 20, 0, 3); //armor, structure, power usage, impulse output
	$impulseDrive->addThruster($warpNacelle);
	$this->addAftSystem($warpNacelle);
		
	$warpNacelle = new TrekWarpDrive(3, 20, 0, 3); //armor, structure, power usage, impulse output
	$impulseDrive->addThruster($warpNacelle);
	$this->addAftSystem($warpNacelle);
		
	$projection = new TrekShieldProjection(3, 18, 7, 270, 90, 'A');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(2, 8, 2, 2, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addAftSystem($projector);
		$projector = new TrekShieldProjector(2, 8, 2, 2, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addAftSystem($projector);
	$this->addAftSystem($projection);
	
		$this->addAftSystem(new LtPlasmaProjector(2, 6, 3, 90, 270));
        	$this->addAftSystem(new EWPointPlasmaGun(2, 3, 2, 90, 270));
        	$this->addAftSystem(new EWPointPlasmaGun(2, 3, 2, 90, 270));
		$this->addAftSystem(new EWDualRocketLauncher(3, 6, 2, 210, 30));
		$this->addAftSystem(new EWDualRocketLauncher(3, 6, 2, 330, 150));
        	$this->addAftSystem(new PlasmaWaveTorpedo(4, 7, 4, 240, 360)); 
        	$this->addAftSystem(new PlasmaWaveTorpedo(4, 7, 4, 0, 120)); 

	//$this->addAftSystem(new SWTractorBeam(2,120,240,1));

		
	//technical thrusters - unlimited, like for LCVs		
	$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 3)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 1)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 2)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 4)); //unhitable and with unlimited thrust allowance  
        $this->addPrimarySystem($impulseDrive);
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 4, 40));
        $this->addAftSystem(new Structure( 3, 40));
	    
        $this->hitChart = array(
            0=> array(
				4 => "2:Nacelle",
				10 => "Structure",
				12 => "Hangar",			
				14 => "Scanner",
				16 => "Engine",
				18 => "Reactor",
				20 => "C&C",
            ),
            1=> array(
		    	2 => "Shield Projector",
				4 => "Point Plasma Gun",
				6 => "Plasma Projector",
				7 => "Light Plasma Projector",
				17 => "Structure",
				20 => "Primary",
            ),
            2=> array(
		    	2 => "Shield Projector",
				6 => "Nacelle",
				8 => "Plasma Projector",
				10 => "Dual Rocket Launcher",
				11 => "Point Plasma Gun",
				12 => "Light Plasma Projector",
				17 => "Structure",
				20 => "Primary",
            ),
       );
	    
	    
    }
}
?>