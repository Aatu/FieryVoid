<?php
class KlingonDaSpu extends HeavyCombatVessel{
	
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 450;
	$this->faction = "StarTrek Klingon";
    $this->phpclass = "KlingonDaSpu";
    $this->imagePath = "img/ships/StarTrek/KlingonDaSpu.png";
    $this->shipClass = "Klingon Da'Spu Escort";

	$this->unofficial = true;
	$this->isd = '2250';

	$this->fighters = array("Shuttlecraft"=>2);
	$this->customFighter = array("Klingon small craft"=>2); //can deploy small craft with Klingon crew
        
    $this->forwardDefense = 13;
    $this->sideDefense = 17;
 
    $this->gravitic = true;       
    $this->turncost = 0.5;
    $this->turndelaycost = 0.66;
    $this->accelcost = 2;
    $this->rollcost = 0;
    $this->pivotcost = 3;
	$this->iniativebonus = 7 *5;

	$this->trueStealth = true;
	$this->canPreOrder = true;

 	$this->addPrimarySystem(new CloakingDevice(4, 16, 4, 0)); 
	$this->addPrimarySystem(new MicroJumpSystem(4, 10, 0, 270, 90, 6, 4));   // Armor, health, power, start arc, end arc, distance, reload time                    
	$this->addPrimarySystem(new CnC(4, 12, 0, 0));
    $this->addPrimarySystem(new Reactor(4, 18, 0, 0));
    $this->addPrimarySystem(new Scanner(4, 12, 5, 4));
	$this->addPrimarySystem(new Hangar(3, 2, 2));

	$impulseDrive = new TrekImpulseDrive(4,16,0,2,3); //Impulse Drive is an engine in its own right, in addition to serving as hub for Nacelle output: $armour, $maxhealth, $powerReq, $output, $boostEfficiency
  
	$projection = new TrekShieldProjection(2, 22, 6, 270, 90, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(2, 6, 2, 2, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addFrontSystem($projector);
		$projector = new TrekShieldProjector(2, 6, 2, 2, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addFrontSystem($projector);
	$this->addFrontSystem($projection);
	$this->addFrontSystem(new TrekDisruptorCannon(4, 8, 6, 240, 360));
	$this->addFrontSystem(new TrekDisruptorCannon(4, 8, 6, 0, 120));
	$this->addFrontSystem(new TrekLightDisruptor(3, 0, 0, 210, 360));
	$this->addFrontSystem(new TrekLightDisruptor(3, 0, 0, 0, 150));


	$warpNacelle = new TrekWarpDrive(3, 33, 0, 6); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
		

		
	$projection = new TrekShieldProjection(2, 16, 6, 90, 270, 'A');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(1, 6, 2, 2, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addAftSystem($projector);
		$projector = new TrekShieldProjector(1, 6, 2, 2, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addAftSystem($projector);
	$this->addAftSystem($projection);

	$this->addAftSystem(new TrekLightDisruptorArray(3, 6, 3, 0, 180));
	$this->addAftSystem(new TrekLightDisruptorArray(3, 6, 3, 180, 360));
	$this->addAftSystem(new TrekLightDisruptor(3, 0, 0, 180, 330));
	$this->addAftSystem(new TrekLightDisruptor(3, 0, 0, 30, 180));

		
	//technical thrusters - unlimited, like for LCVs		
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance  
    $this->addPrimarySystem($impulseDrive);
        
    //0:primary, 1:front, 2:rear, 3:left, 4:right;
    $this->addFrontSystem(new Structure( 4, 40));
    $this->addPrimarySystem(new Structure( 4, 36));
    $this->addAftSystem(new Structure( 4, 36));  
	    
        $this->hitChart = array(
            0=> array(
                8 => "Structure",
                11 => "Scanner",
                13 => "Engine",
                15 => "Hangar",
                16 => "Warp Drive",
                17 => "Cloaking Device",
                19 => "Reactor",
                20 => "C&C",
            ),
            1=> array(
				2 => "Shield Projector",
				6 => "Disruptor Cannon",
				9 => "Light Disruptor",
				18 => "Structure",
				20 => "Primary",
            ),
            2=> array(
				2 => "Shield Projector",
				7 => "Nacelle",
				10 => "Disruptor Cannon",
				12 => "Light Disruptor Array",
				13 => "Light Disruptor",
				17 => "Structure",
				20 => "Primary",
            ),
       );
	    
	    
    }
}
?>