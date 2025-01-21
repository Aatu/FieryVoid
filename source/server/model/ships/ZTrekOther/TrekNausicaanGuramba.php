<?php
class TrekNausicaanGuramba extends BaseShip{
	
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 900;
        $this->faction = "ZTrek Playtest Other Factions";
        $this->phpclass = "TrekNausicaanGuramba";
        $this->imagePath = "img/ships/StarTrek/NausicaanGuramba.png";
        $this->shipClass = "Nausicaan Guramba Siege Ship";

	$this->limited = 33;
	$this->unofficial = true;
	    $this->isd = '2272';

	$this->fighters = array("Shuttlecraft"=>4);
	$this->customFighter = array("Nausicaan small craft"=>4); //can deploy small craft with Nausicaan crew, including assault craft
        
        $this->forwardDefense = 17;
        $this->sideDefense = 17;
 
        $this->gravitic = true;       
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 5;
	$this->iniativebonus = -1 *5; 
		
	$this->addPrimarySystem(new CnC(4, 24, 0, 0));
        $this->addPrimarySystem(new Reactor(4, 38, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 24, 5, 7));
	$this->addPrimarySystem(new Hangar(3, 4, 2));

	$impulseDrive = new TrekImpulseDrive(4,30,0,2,4); //Impulse Drive is an engine in its own right, in addition to serving as hub for Nacelle output: $armour, $maxhealth, $powerReq, $output, $boostEfficiency
		
	$projection = new TrekShieldProjection(3, 40, 7, 300, 60, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(2, 8, 2, 2, 300, 60, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addFrontSystem($projector);
		$projector = new TrekShieldProjector(2, 8, 2, 2, 300, 60, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addFrontSystem($projector);
		$projector = new TrekShieldProjector(2, 8, 2, 2, 300, 60, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addFrontSystem($projector);
	$this->addFrontSystem($projection);
	$this->addFrontSystem(new PlasmaSiegeCannon(3, 0, 0, 300, 60));
	$this->addFrontSystem(new PlasmaSiegeCannon(3, 0, 0, 300, 60));
	$this->addFrontSystem(new EWPointPlasmaGun(2, 3, 2, 240, 90));
	$this->addFrontSystem(new EWPointPlasmaGun(2, 3, 2, 270, 120));

	$projection = new TrekShieldProjection(3, 24, 7, 210, 330, 'L');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(2, 8, 2, 2, 210, 330, 'L'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addLeftSystem($projector);
		$projector = new TrekShieldProjector(2, 8, 2, 2, 210, 330, 'L'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addLeftSystem($projector);
	$this->addLeftSystem($projection);
	$this->addLeftSystem(new PlasmaProjector(4, 8, 5, 240, 360));
	$this->addLeftSystem(new EWDualRocketLauncher(3, 6, 2, 210, 30));

	$projection = new TrekShieldProjection(3, 24, 7, 30, 150, 'R');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(2, 8, 2, 2, 30, 150, 'R'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addRightSystem($projector);
		$projector = new TrekShieldProjector(2, 8, 2, 2, 30, 150, 'R'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addRightSystem($projector);
	$this->addRightSystem($projection);
	$this->addRightSystem(new PlasmaProjector(4, 8, 5, 0, 120));
	$this->addRightSystem(new EWDualRocketLauncher(3, 6, 2, 330, 150));

	$warpNacelle = new TrekWarpDrive(4, 24, 0, 2); //armor, structure, power usage, impulse output
	$impulseDrive->addThruster($warpNacelle);
	$this->addAftSystem($warpNacelle);
	$warpNacelle = new TrekWarpDrive(4, 24, 0, 2); //armor, structure, power usage, impulse output
	$impulseDrive->addThruster($warpNacelle);
	$this->addAftSystem($warpNacelle);
	$warpNacelle = new TrekWarpDrive(4, 24, 0, 2); //armor, structure, power usage, impulse output
	$impulseDrive->addThruster($warpNacelle);
	$this->addAftSystem($warpNacelle);
		
	$projection = new TrekShieldProjection(2, 18, 6, 120, 240, 'A');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(2, 6, 2, 2, 120, 240, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addAftSystem($projector);
		$projector = new TrekShieldProjector(2, 6, 2, 2, 120, 240, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addAftSystem($projector);
	$this->addAftSystem($projection);
        	$this->addAftSystem(new EWPointPlasmaGun(2, 3, 2, 90, 270));
        	$this->addAftSystem(new EWPointPlasmaGun(2, 3, 2, 90, 270));
		
	//technical thrusters - unlimited, like for LCVs		
	$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 3)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 1)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 2)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 1, 0, 99, 4)); //unhitable and with unlimited thrust allowance  
        $this->addPrimarySystem($impulseDrive);
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 50));
        $this->addAftSystem(new Structure( 3, 36));
        $this->addLeftSystem(new Structure( 4, 40));
        $this->addRightSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 4, 50));

	    
        $this->hitChart = array(
            0=> array(
				8 => "Structure",
				9 => "Hangar",
				11 => "2:Nacelle",
				14 => "Scanner",
				17 => "Engine",
				19 => "Reactor",
				20 => "C&C",
            ),
            1=> array(
				3 => "Shield Projector",
				8 => "Plasma Siege Cannon",
				10 => "Point Plasma Gun",
				18 => "Structure",
				20 => "Primary",
            ),
            2=> array(
				2 => "Shield Projector",
				9 => "Nacelle",
				11 => "Point Plasma Gun",
				18 => "Structure",
				20 => "Primary",
            ),
            3=> array(
				2 => "Shield Projector",
				4 => "2:Nacelle",
				7 => "Plasma Projector",
				10 => "Dual Rocket Launcher",
				18 => "Structure",
				20 => "Primary",
            ),
            4=> array(
				2 => "Shield Projector",
				4 => "2:Nacelle",
				7 => "Plasma Projector",
				10 => "Dual Rocket Launcher",
				18 => "Structure",
				20 => "Primary",
            ),
       );
	    
	    
    }
}
?>