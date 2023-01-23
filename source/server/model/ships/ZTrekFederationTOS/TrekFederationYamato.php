<?php
class TrekFederationYamato extends BaseShip{
	
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 760;
        $this->faction = "ZStarTrek Federation (TOS)";
        $this->phpclass = "TrekFederationYamato";
        $this->imagePath = "img/ships/StarTrek/FederationCarrierYamato.png";
        $this->shipClass = "Yamato Carrier";

	$this->limited = 10;
	$this->unofficial = true;
	    $this->isd = '2269';

        $this->fighters = array("heavy"=>36);
		$this->customFighter = array("Human small craft"=>36); //can deploy small craft with Human crew
        
        $this->forwardDefense = 16;
        $this->sideDefense = 18;
 
        $this->gravitic = true;       
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 5;
	$this->iniativebonus = -2 *5; 
		
	$this->addPrimarySystem(new CnC(4, 24, 0, 0));
        $this->addPrimarySystem(new Reactor(4, 30, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 24, 5, 7));

	$impulseDrive = new TrekImpulseDrive(4,30,0,1,4); //Impulse Drive is an engine in its own right, in addition to serving as hub for Nacelle output: $armour, $maxhealth, $powerReq, $output, $boostEfficiency
		
	$projection = new TrekShieldProjection(2, 30, 6, 300, 60, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(2, 6, 2, 2, 300, 60, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addFrontSystem($projector);
		$projector = new TrekShieldProjector(2, 6, 2, 2, 300, 60, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addFrontSystem($projector);
	$this->addFrontSystem($projection);
	$this->addFrontSystem(new SWTractorBeam(3,0,360,2)); 
	$this->addFrontSystem(new TrekLightPhaser(3, 0, 0, 270, 90));
        $this->addFrontSystem(new TrekLightPhaser(3, 0, 0, 270, 90));
	$this->addFrontSystem(new TrekPhaserLance(3, 0, 0, 270, 30));
	$this->addFrontSystem(new TrekPhaserLance(3, 0, 0, 330, 90));

	$projection = new TrekShieldProjection(2, 30, 6, 210, 330, 'L');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(2, 6, 2, 2, 210, 330, 'L'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addLeftSystem($projector);
		$projector = new TrekShieldProjector(2, 6, 2, 2, 210, 330, 'L'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addLeftSystem($projector);
	$this->addLeftSystem($projection);
        $this->addLeftSystem(new TrekPhotonTorp(3, 0, 0, 240, 360));
	$this->addLeftSystem(new TrekLightPhaser(3, 0, 0, 210, 330));
	$this->addLeftSystem(new TrekLightPhaser(3, 0, 0, 210, 330));
	$this->addLeftSystem(new TrekPhaser(3, 0, 0, 210, 330));

	$projection = new TrekShieldProjection(2, 30, 6, 30, 150, 'R');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(2, 6, 2, 2, 30, 150, 'R'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addRightSystem($projector);
		$projector = new TrekShieldProjector(2, 6, 2, 2, 30, 150, 'R'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addRightSystem($projector);
	$this->addRightSystem($projection);
	$this->addRightSystem(new TrekPhotonTorp(3, 0, 0, 0, 120));
	$this->addRightSystem(new TrekLightPhaser(3, 0, 0, 30, 150));
	$this->addRightSystem(new TrekLightPhaser(3, 0, 0, 30, 150));
	$this->addRightSystem(new TrekPhaser(3, 0, 0, 30, 150));

	$warpNacelle = new TrekWarpDrive(4, 24, 0, 2); //armor, structure, power usage, impulse output
	$impulseDrive->addThruster($warpNacelle);
	$this->addAftSystem($warpNacelle);
	$warpNacelle = new TrekWarpDrive(4, 24, 0, 2); //armor, structure, power usage, impulse output
	$impulseDrive->addThruster($warpNacelle);
	$this->addAftSystem($warpNacelle);
	$warpNacelle = new TrekWarpDrive(4, 24, 0, 2); //armor, structure, power usage, impulse output
	$impulseDrive->addThruster($warpNacelle);
	$this->addAftSystem($warpNacelle);
	$warpNacelle = new TrekWarpDrive(4, 24, 0, 2); //armor, structure, power usage, impulse output
	$impulseDrive->addThruster($warpNacelle);
	$this->addAftSystem($warpNacelle);		

		
	$projection = new TrekShieldProjection(2, 30, 6, 120, 240, 'A');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(2, 6, 2, 2, 120, 240, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addAftSystem($projector);
		$projector = new TrekShieldProjector(2, 6, 2, 2, 120, 240, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addAftSystem($projector);
	$this->addAftSystem($projection);
        $this->addAftSystem(new Hangar(4, 18));
        $this->addAftSystem(new Hangar(4, 18));
	$this->addAftSystem(new TrekLightPhaser(3, 0, 0, 90, 270));
	$this->addAftSystem(new TrekLightPhaser(3, 0, 0, 90, 270));
		
	//technical thrusters - unlimited, like for LCVs		
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance  
        $this->addPrimarySystem($impulseDrive);
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 45));
        $this->addAftSystem(new Structure( 4, 40));
        $this->addLeftSystem(new Structure( 4, 40));
        $this->addRightSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 4, 55));

	    
        $this->hitChart = array(
            0=> array(
				8 => "Structure",
				10 => "2:Nacelle",
				13 => "Scanner",
				17 => "Engine",
				19 => "Reactor",
				20 => "C&C",
            ),
            1=> array(
				2 => "Shield Projector",
				5 => "Light Phaser",
				7 => "Tractor Beam",
				11 => "Phaser Lance",
				18 => "Structure",
				20 => "Primary",
            ),
            2=> array(
				2 => "Shield Projector",
				6 => "Nacelle",
				9 => "Hangar",
				11 => "Light Phaser",
				18 => "Structure",
				20 => "Primary",
            ),
            3=> array(
				2 => "Shield Projector",
				4 => "2:Nacelle",
				6 => "Photon Torpedo",
				8 => "Light Phaser",
				10 => "Phaser",
				18 => "Structure",
				20 => "Primary",
            ),
            4=> array(
				2 => "Shield Projector",
				4 => "2:Nacelle",
				6 => "Photon Torpedo",
				8 => "Light Phaser",
				10 => "Phaser",
				18 => "Structure",
				20 => "Primary",
            ),
       );
	    
	    
    }
}
?>