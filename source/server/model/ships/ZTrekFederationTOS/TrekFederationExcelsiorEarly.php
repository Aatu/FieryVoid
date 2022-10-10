<?php
class TrekFederationExcelsiorEarly extends BaseShipNoAft{
	
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 825;
	$this->faction = "ZTrek Playtest Federation (TOS)";
        $this->phpclass = "TrekFederationExcelsiorEarly";
        $this->imagePath = "img/ships/StarTrek/FederationExcelsior.png";
        $this->shipClass = "Excelsior Early Cruiser";

	$this->unofficial = true;
	    $this->isd = '2285';

	$this->fighters = array("Shuttlecraft"=>6);
		$this->customFighter = array("Human small craft"=>6); //can deploy small craft with Human crew
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
 
        $this->gravitic = true;       
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 4;
	$this->iniativebonus = 2 *5; 
		
	$this->addPrimarySystem(new CnC(5, 20, 0, 0));
        $this->addPrimarySystem(new Reactor(5, 30, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 18, 7, 7));
	$this->addPrimarySystem(new Hangar(4, 6, 6));
	$this->addAftSystem(new TrekPhotonTorp(3, 0, 0, 120, 240));
	$this->addAftSystem(new TrekPhaserLance(3, 0, 0, 120, 240)); 

  
	$impulseDrive = new TrekImpulseDrive(5,30,0,3,3); //Impulse Drive is an engine in its own right, in addition to serving as hub for Nacelle output: $armour, $maxhealth, $powerReq, $output, $boostEfficiency
	
	$projection = new TrekShieldProjection(3, 30, 7, 300, 60, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(2, 8, 2, 3, 300, 60, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addFrontSystem($projector);
		$projector = new TrekShieldProjector(2, 8, 2, 3, 300, 60, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addFrontSystem($projector);
	$this->addFrontSystem($projection);
	$this->addFrontSystem(new TrekPhotonTorp(3, 0, 0, 270, 90));
        $this->addFrontSystem(new TrekPhotonTorp(3, 0, 0, 270, 90));
	$this->addFrontSystem(new TrekPhaserLance(3, 0, 0, 270, 30));
	$this->addFrontSystem(new TrekPhaserLance(3, 0, 0, 330, 90));
	$this->addFrontSystem(new SWTractorBeam(4,0,360,2)); 


	$projection = new TrekShieldProjection(2, 30, 6, 180, 300, 'L');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(2, 8, 2, 3, 180, 300, 'L'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addLeftSystem($projector);
		$projector = new TrekShieldProjector(2, 8, 2, 3, 180, 300, 'L'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addLeftSystem($projector);
	$this->addLeftSystem($projection);
	$this->addLeftSystem(new TrekPhaser(3, 0, 0, 240, 360));
	$this->addLeftSystem(new TrekPhaser(3, 0, 0, 210, 330));
	$warpNacelle = new TrekWarpDrive(4, 25, 0, 4); //armor, structure, power usage, impulse output
	$impulseDrive->addThruster($warpNacelle);
	$this->addLeftSystem($warpNacelle);


	$projection = new TrekShieldProjection(2, 30, 6, 60, 180, 'R');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(2, 8, 2, 3, 60, 180, 'R'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addRightSystem($projector);
		$projector = new TrekShieldProjector(2, 8, 2, 3, 60, 180, 'R'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addRightSystem($projector);
	$this->addRightSystem($projection);
	$this->addRightSystem(new TrekPhaser(3, 0, 0, 0, 120));
	$this->addRightSystem(new TrekPhaser(3, 0, 0, 30, 150));
	$warpNacelle = new TrekWarpDrive(4, 25, 0, 4); //armor, structure, power usage, impulse output
	$impulseDrive->addThruster($warpNacelle);
	$this->addRightSystem($warpNacelle);



		
	//drive system - Impulse Drive and technical Thrusters	
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance  
        $this->addPrimarySystem($impulseDrive);
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 45));
        $this->addLeftSystem(new Structure( 4, 45));
        $this->addRightSystem(new Structure( 4, 45));
        $this->addPrimarySystem(new Structure( 4, 40));

	    
        $this->hitChart = array(
            0=> array(
				7 => "Structure",
				9 => "2:Phaser Lance",
				10 => "2:Photon Torpedo",
				13 => "Scanner",
				16 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
            ),
            1=> array(
				2 => "Shield Projector",
				6 => "Phaser Lance",
				9 => "Photon Torpedo",
				11 => "Tractor Beam",
				18 => "Structure",
				20 => "Primary",
            ),
            3=> array(
				2 => "Shield Projector",
				7 => "Nacelle",
				10 => "Phaser",
				18 => "Structure",
				20 => "Primary",
            ),
            4=> array(
				2 => "Shield Projector",
				7 => "Nacelle",
				10 => "Phaser",
				18 => "Structure",
				20 => "Primary",
            ),
       );
	    
	    
    }
}
?>