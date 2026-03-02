<?php
class KlingonD7Early extends HeavyCombatVessel{
	
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 525;
	$this->faction = "ZStarTrek Klingon";
    $this->phpclass = "KlingonD7Early";
    $this->imagePath = "img/ships/StarTrek/KlingonD7.png";
    $this->shipClass = "Klingon D7 Cruiser (Early)";
		$this->occurence = "common";
		$this->variantOf = "Klingon D7 Cruiser";

	$this->unofficial = true;
	$this->isd = '2150';

	$this->fighters = array("Shuttlecraft"=>2);
	$this->customFighter = array("Klingon small craft"=>2); //can deploy small craft with Klingon crew
        
    $this->forwardDefense = 13;
    $this->sideDefense = 16;
 
    $this->gravitic = true;       
    $this->turncost = 0.5;
    $this->turndelaycost = 0.5;
    $this->accelcost = 2;
    $this->rollcost = 2;
    $this->pivotcost = 2;
	$this->iniativebonus = 7 *5;

	$this->trueStealth = true;
	$this->canPreOrder = true; 
		
 	$this->addPrimarySystem(new CloakingDevice(3, 10, 4, 0));
	$this->addPrimarySystem(new MicroJumpSystem(3, 8, 0, 330, 30, 6, 4));   // Armor, health, power, start arc, end arc, distance, reload time 
	$this->addPrimarySystem(new CnC(4, 10, 0, 0));
    $this->addPrimarySystem(new Reactor(4, 20, 0, 0));
    $this->addPrimarySystem(new Scanner(4, 12, 6, 5));
	$this->addPrimarySystem(new Hangar(3, 2, 2));

	$impulseDrive = new TrekImpulseDrive(4,24,0,2,3); //Impulse Drive is an engine in its own right, in addition to serving as hub for Nacelle output: $armour, $maxhealth, $powerReq, $output, $boostEfficiency
  
	$projection = new TrekShieldProjection(2, 24, 6, 270, 90, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(1, 6, 2, 2, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addFrontSystem($projector);
		$projector = new TrekShieldProjector(1, 6, 2, 2, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addFrontSystem($projector);
	$this->addFrontSystem($projection);
	$this->addFrontSystem(new TrekKlingonLauncher(3, 0, 0, 270, 90));
	$this->addFrontSystem(new TrekLightDisruptor(3, 0, 0, 240, 60));
	$this->addFrontSystem(new TrekLightDisruptor(3, 0, 0, 300, 120));

	$warpNacelle = new TrekWarpDrive(4, 24, 0, 4); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
		
	$warpNacelle = new TrekWarpDrive(4, 24, 0, 4); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
		
	$projection = new TrekShieldProjection(2, 18, 6, 90, 270, 'A');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(1, 6, 2, 2, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addAftSystem($projector);
		$projector = new TrekShieldProjector(1, 6, 2, 2, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addAftSystem($projector);
	$this->addAftSystem($projection);
	$this->addAftSystem(new TrekEarlyDisruptor(3, 0, 0, 240, 30));
	$this->addAftSystem(new TrekEarlyDisruptor(3, 0, 0, 330, 120));
	$this->addAftSystem(new TrekLightDisruptor(3, 0, 0, 180, 360));
	$this->addAftSystem(new TrekLightDisruptor(3, 0, 0, 0, 180));
		
	//technical thrusters - unlimited, like for LCVs		
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance  
    $this->addPrimarySystem($impulseDrive);
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 38));
        $this->addPrimarySystem(new Structure( 4, 36));
       $this->addAftSystem(new Structure( 3, 36));  
	    
        $this->hitChart = array(
            0=> array(
                8 => "Structure",
                11 => "Scanner",
                13 => "Engine",
                15 => "Hangar",
                16 => "Micro Jump Drive",
                17 => "Cloaking Device",
                19 => "Reactor",
                20 => "C&C",
            ),
            1=> array(
				2 => "Shield Projector",
				5 => "Klingon Launcher",
				8 => "Light Disruptor",
				17 => "Structure",
				20 => "Primary",
            ),
            2=> array(
				2 => "Shield Projector",
				8 => "Nacelle",
				11 => "Medium Disruptor",
				13 => "Light Disruptor",
				18 => "Structure",
				20 => "Primary",
            ),
       );
	    
	    
    }
}

?>