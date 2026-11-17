<?php
class KlingonQugh extends BaseShipNoAft{
	
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 600;
	$this->faction = "StarTrek Klingon";
    $this->phpclass = "KlingonQugh";
    $this->imagePath = "img/ships/StarTrek/KlingonQugh.png";
    $this->shipClass = "Klingon Qugh Battlecruiser";

	$this->unofficial = true;
	$this->isd = '2230';

	$this->fighters = array("Shuttlecraft"=>6);
	$this->customFighter = array("Klingon small craft"=>2); //can deploy small craft with Klingon crew
        
    $this->forwardDefense = 15;
    $this->sideDefense = 17;
 
    $this->gravitic = true;       
    $this->turncost = 0.66;
    $this->turndelaycost = 0.66;
    $this->accelcost = 3;
    $this->rollcost = 2;
    $this->pivotcost = 3;
	$this->iniativebonus = 1 *5;

	$this->trueStealth = true;
	$this->canPreOrder = true;

 	$this->addPrimarySystem(new CloakingDevice(4, 16, 4, 0)); 
	$this->addPrimarySystem(new MicroJumpSystem(4, 16, 0, 270, 90, 6, 4));   // Armor, health, power, start arc, end arc, distance, reload time                    
	$this->addPrimarySystem(new CnC(4, 16, 0, 0));
    $this->addPrimarySystem(new Reactor(4, 24, 0, 0));
    $this->addPrimarySystem(new Scanner(4, 18, 6, 5));
	$this->addPrimarySystem(new Hangar(3, 6, 6));

	$impulseDrive = new TrekImpulseDrive(4,28,0,0,3); //Impulse Drive is an engine in its own right, in addition to serving as hub for Nacelle output: $armour, $maxhealth, $powerReq, $output, $boostEfficiency
  
	$projection = new TrekShieldProjection(2, 24, 6, 270, 90, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(2, 6, 2, 2, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addFrontSystem($projector);
		$projector = new TrekShieldProjector(2, 6, 2, 2, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addFrontSystem($projector);
	$this->addFrontSystem($projection);
	$this->addFrontSystem(new TrekDisruptorCannon(4, 8, 6, 300, 60));
	$this->addFrontSystem(new TrekDisruptorCannon(4, 8, 6, 300, 60));
	$this->addFrontSystem(new TrekPhotonicTorp(2, 6, 1, 270, 60));
	$this->addFrontSystem(new TrekPhotonicTorp(2, 6, 1, 300, 90));
	$this->addFrontSystem(new TrekLightDisruptorArray(3, 6, 3, 240, 120));

	$warpNacelle = new TrekWarpDrive(4, 25, 0, 5); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addLeftSystem($warpNacelle);
	$projection = new TrekShieldProjection(2, 20, 6, 180, 300, 'L');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(1, 6, 2, 2, 180, 300, 'L'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addLeftSystem($projector);
		$projector = new TrekShieldProjector(1, 6, 2, 2, 180, 300, 'L'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addLeftSystem($projector);
	$this->addLeftSystem($projection);
	$this->addLeftSystem(new TrekLightDisruptorArray(3, 6, 3, 120, 360));
	$this->addLeftSystem(new TrekLightDisruptor(3, 0, 0, 180, 0));	
	$this->addLeftSystem(new TrekPhotonicTorp(2, 6, 1, 150, 270));

	$warpNacelle = new TrekWarpDrive(4, 25, 0, 5); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addRightSystem($warpNacelle);
	$projection = new TrekShieldProjection(2, 20, 6, 60, 180, 'R');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(1, 6, 2, 2, 60, 180, 'R'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addRightSystem($projector);
		$projector = new TrekShieldProjector(1, 6, 2, 2, 60, 180, 'R'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addRightSystem($projector);
	$this->addRightSystem($projection);
	$this->addRightSystem(new TrekLightDisruptorArray(3, 6, 3, 0, 240));
	$this->addRightSystem(new TrekLightDisruptor(3, 0, 0, 0, 180));
	$this->addRightSystem(new TrekPhotonicTorp(2, 6, 1, 90, 210));	

	//technical thrusters - unlimited, like for LCVs		
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance  
    $this->addPrimarySystem($impulseDrive);
        
    //0:primary, 1:front, 2:rear, 3:left, 4:right;
    $this->addFrontSystem(new Structure( 4, 44));
    $this->addPrimarySystem(new Structure( 4, 44));
    $this->addLeftSystem(new Structure( 3, 44));
    $this->addRightSystem(new Structure( 3, 44));  
	    
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
				5 => "Disruptor Cannon",
				8 => "Photonic Torpedo",
				9 => "Light Disruptor Array",
				18 => "Structure",
				20 => "Primary",
            ),
            3=> array(
				2 => "Shield Projector",
				7 => "Nacelle",
				9 => "Photonic Torpedo",
				11 => "Light Disruptor Array",
				13 => "Light Disruptor",
				17 => "Structure",
				20 => "Primary",
            ),
            4=> array(
				2 => "Shield Projector",
				7 => "Nacelle",
				9 => "Photonic Torpedo",
				11 => "Light Disruptor Array",
				13 => "Light Disruptor",
				17 => "Structure",
				20 => "Primary",
            ),
       );
	    
	    
    }
}
?>