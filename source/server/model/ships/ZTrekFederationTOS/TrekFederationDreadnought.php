<?php
class TrekFederationDreadnought extends HeavyCombatVessel{
	
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 650;
	$this->faction = "ZTrek Playtest Federation (TOS)";
        $this->phpclass = "TrekFederationDreadnought";
        $this->imagePath = "img/ships/StarTrek/FederationDN.png";
        $this->shipClass = "Dreadnought";

        $this->limited = 33;	
	$this->unofficial = true;
	    $this->isd = '2260';


	$this->fighters = array("Shuttlecraft"=>6);
		$this->customFighter = array("Human small craft"=>6); //can deploy small craft with Human crew
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
 
        $this->gravitic = true;       
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
	$this->iniativebonus = 6 *5; 
		
	$this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new Reactor(4, 28, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 18, 6, 6));
	$this->addPrimarySystem(new Hangar(3, 6, 6));

	$impulseDrive = new TrekImpulseDrive(4,26,0,1,3); //Impulse Drive is an engine in its own right, in addition to serving as hub for Nacelle output: $armour, $maxhealth, $powerReq, $output, $boostEfficiency
		
  
	$projection = new TrekShieldProjection(2, 28, 6, 270, 90, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(2, 8, 2, 3, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addFrontSystem($projector);
		$projector = new TrekShieldProjector(2, 8, 2, 3, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addFrontSystem($projector);
	$this->addFrontSystem($projection);
	$this->addFrontSystem(new TrekPhotonTorp(3, 0, 0, 270, 90));
        $this->addFrontSystem(new TrekPhotonTorp(3, 0, 0, 270, 90));
	$this->addFrontSystem(new TrekPhaserLance(3, 0, 0, 270, 30));
	$this->addFrontSystem(new TrekPhaserLance(3, 0, 0, 300, 60));
	$this->addFrontSystem(new TrekPhaserLance(3, 0, 0, 330, 90));
	$this->addFrontSystem(new SWTractorBeam(3,0,360,2));

	$warpNacelle = new TrekWarpDrive(4, 24, 0, 3); //armor, structure, power usage, impulse output
	$impulseDrive->addThruster($warpNacelle);
	$this->addAftSystem($warpNacelle);
		
	$warpNacelle = new TrekWarpDrive(4, 24, 0, 3); //armor, structure, power usage, impulse output
	$impulseDrive->addThruster($warpNacelle);
	$this->addAftSystem($warpNacelle);

	$warpNacelle = new TrekWarpDrive(4, 24, 0, 3); //armor, structure, power usage, impulse output
	$impulseDrive->addThruster($warpNacelle);
	$this->addAftSystem($warpNacelle);
		
	$projection = new TrekShieldProjection(2, 24, 6, 90, 270, 'A');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(2, 6, 2, 2, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addAftSystem($projector);
		$projector = new TrekShieldProjector(2, 6, 2, 2, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addAftSystem($projector);
	$this->addAftSystem($projection);
	
        $this->addAftSystem(new Scanner(4, 10, 2, 2));
        $this->addAftSystem(new TrekPhotonTorp(3, 0, 0, 120, 240));
        $this->addAftSystem(new TrekPhotonTorp(3, 0, 0, 120, 240));
	$this->addAftSystem(new TrekPhaserLance(3, 0, 0, 120, 240));

	//$this->addAftSystem(new SWTractorBeam(2,120,240,1));

		
	//technical thrusters - unlimited, like for LCVs		
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance  
        $this->addPrimarySystem($impulseDrive);
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 45));
        $this->addPrimarySystem(new Structure( 4, 45));
        $this->addAftSystem(new Structure( 4, 45));
	    
        $this->hitChart = array(
            0=> array(
                    7 => "Structure",
					9 => "2:Nacelle",
                    12 => "Scanner",
                    14 => "Engine",
                    16 => "Hangar",
                    18 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
				2 => "Shield Projector",
				6 => "Phaser Lance",
				9 => "Photon Torpedo",
				10 => "Tractor Beam",
				18 => "Structure",
				20 => "Primary",
            ),
            2=> array(
				2 => "Shield Projector",
				7 => "Nacelle",
				9 => "Scanner",
				11 => "Phaser Lance",
				13 => "Photon Torpedo",
				18 => "Structure",
				20 => "Primary",
            ),
       );
	    
	    
    }
}
?>
