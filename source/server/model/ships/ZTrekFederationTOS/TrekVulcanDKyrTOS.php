<?php
class TrekVulcanDkyrTOS extends BaseShip{
	
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 840;
	$this->faction = "ZStarTrek Federation (TOS)";
        $this->phpclass = "TrekVulcanDkyrTOS";
        $this->imagePath = "img/ships/StarTrek/VulcanDKyr.png";
        $this->shipClass = "Vulcan D'Kyr Cruiser (TOS era)";

	$this->unofficial = true;
	    $this->isd = '2250';


	$this->fighters = array("Shuttlecraft"=>4);
		$this->customFighter = array("Vulcan small craft"=>4); //can deploy small craft with Vulcan crew
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
 
        $this->gravitic = true;       
        $this->turncost = 0.75;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
	$this->iniativebonus = 0; 
		
	$this->addPrimarySystem(new CnC(5, 20, 0, 0));
        $this->addPrimarySystem(new Reactor(5, 24, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 18, 6, 8));
	$this->addPrimarySystem(new Hangar(4, 4, 4));
	$this->addPrimarySystem(new SWTractorBeam(3,0,360,2)); 

	$impulseDrive = new TrekImpulseDrive(4,30,0,3,3); //Impulse Drive is an engine in its own right, in addition to serving as hub for Nacelle output: $armour, $maxhealth, $powerReq, $output, $boostEfficiency
		
  
	$projection = new TrekShieldProjection(3, 24, 8, 300, 60, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(2, 8, 2, 3, 300, 60, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addFrontSystem($projector);
		$projector = new TrekShieldProjector(2, 8, 2, 3, 300, 60, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addFrontSystem($projector);
	$this->addFrontSystem($projection);
	$this->addFrontSystem(new TrekPhaserLance(4, 0, 0, 270, 30));
	$this->addFrontSystem(new TrekPhaserLance(4, 0, 0, 330, 90));
	$this->addFrontSystem(new TrekPhaser(4, 0, 0, 240, 0));
	$this->addFrontSystem(new TrekPhaser(4, 0, 0, 0, 120));

	$projection = new TrekShieldProjection(3, 24, 8, 210, 330, 'L');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(2, 8, 2, 3, 210, 330, 'L'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addLeftSystem($projector);
		$projector = new TrekShieldProjector(2, 8, 2, 3, 210, 330, 'L'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addLeftSystem($projector);
	$this->addLeftSystem($projection);
	$this->addLeftSystem(new TrekPhotonTorp(4, 0, 0, 240, 0));
	$this->addLeftSystem(new TrekPhotonTorp(4, 0, 0, 240, 0));
	$this->addLeftSystem(new TrekLightPhaserLance(3, 0, 0, 180, 60));

	$projection = new TrekShieldProjection(3, 24, 8, 30, 150, 'R');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(2, 8, 2, 3, 30, 150, 'R'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addRightSystem($projector);
		$projector = new TrekShieldProjector(2, 8, 2, 3, 30, 150, 'R'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addRightSystem($projector);
	$this->addRightSystem($projection);
	$this->addRightSystem(new TrekPhotonTorp(4, 0, 0, 0, 120));
	$this->addRightSystem(new TrekPhotonTorp(4, 0, 0, 0, 120));
	$this->addRightSystem(new TrekLightPhaserLance(3, 0, 0, 300, 180));

	$warpNacelle = new TrekWarpDrive(4, 16, 0, 2); //armor, structure, power usage, impulse output
	$impulseDrive->addThruster($warpNacelle);
	$this->addAftSystem($warpNacelle);
	$warpNacelle = new TrekWarpDrive(4, 16, 0, 2); //armor, structure, power usage, impulse output
	$impulseDrive->addThruster($warpNacelle);
	$this->addAftSystem($warpNacelle);
	$warpNacelle = new TrekWarpDrive(4, 16, 0, 2); //armor, structure, power usage, impulse output
	$impulseDrive->addThruster($warpNacelle);
	$this->addAftSystem($warpNacelle);
	$warpNacelle = new TrekWarpDrive(4, 16, 0, 2); //armor, structure, power usage, impulse output
	$impulseDrive->addThruster($warpNacelle);
	$this->addAftSystem($warpNacelle);		

		
	$projection = new TrekShieldProjection(3, 24, 8, 120, 240, 'A');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(2, 8, 2, 3, 120, 240, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addAftSystem($projector);
		$projector = new TrekShieldProjector(2, 8, 2, 3, 120, 240, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addAftSystem($projector);
	$this->addAftSystem($projection);
	$this->addAftSystem(new TrekLightPhaserLance(3, 0, 0, 90, 270));
	$this->addAftSystem(new TrekLightPhaserLance(3, 0, 0, 90, 270));

		
	//technical thrusters - unlimited, like for LCVs		
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance  
        $this->addPrimarySystem($impulseDrive);
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 40));
        $this->addAftSystem(new Structure( 4, 36));
        $this->addLeftSystem(new Structure( 4, 44));
        $this->addRightSystem(new Structure( 4, 44));
        $this->addPrimarySystem(new Structure( 5, 40));

	    
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
				7 => "Phaser Lance",
				10 => "Phaser",
				18 => "Structure",
				20 => "Primary",
            ),
            2=> array(
				2 => "Shield Projector",
				9 => "Nacelle",
				11 => "Light Phaser Lance",
				18 => "Structure",
				20 => "Primary",
            ),
            3=> array(
				2 => "Shield Projector",
				7 => "Photon Torpedo",
				9 => "Light Phaser Lance",
				18 => "Structure",
				20 => "Primary",
            ),
            4=> array(
				2 => "Shield Projector",
				7 => "Photon Torpedo",
				9 => "Light Phaser Lance",
				18 => "Structure",
				20 => "Primary",
            ),
       );
	    
	    
    }
}
?>