<?php
class TrekFederationIllustrious extends HeavyCombatVessel{
	
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 525;
	$this->faction = "ZTrek Playtest Federation (TOS)";
        $this->phpclass = "TrekFederationIllustrious";
        $this->imagePath = "img/ships/StarTrek/Constitution.png";
        $this->shipClass = "Illustrious Battle Carrier";

			$this->occurence = "uncommon";
			$this->variantOf = "Constitution Light Cruiser";
			
	$this->unofficial = true;
	    $this->isd = '2276';


        $this->fighters = array("heavy"=>12, "Shuttlecraft"=>2);
		$this->customFighter = array("Human small craft"=>14); //can deploy small craft with Human crew
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
 
        $this->gravitic = true;       
        $this->turncost = 0.66;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
	$this->iniativebonus = 6 *5; 
		
	$this->addPrimarySystem(new CnC(4, 10, 0, 0));
        $this->addPrimarySystem(new Reactor(4, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 6, 6));
	$this->addPrimarySystem(new Hangar(3, 14, 6));

	$impulseDrive = new TrekImpulseDrive(4,26,0,1,3); //Impulse Drive is an engine in its own right, in addition to serving as hub for Nacelle output: $armour, $maxhealth, $powerReq, $output, $boostEfficiency
		
  
	$projection = new TrekShieldProjection(2, 24, 6, 270, 90, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(1, 6, 2, 2, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addFrontSystem($projector);
		$projector = new TrekShieldProjector(1, 6, 2, 2, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addFrontSystem($projector);
	$this->addFrontSystem($projection);
	$this->addFrontSystem(new TrekPhotonTorp(3, 0, 0, 270, 90));
        $this->addFrontSystem(new TrekPhotonTorp(3, 0, 0, 270, 90));
	$this->addFrontSystem(new TrekPhaser(3, 0, 0, 270, 30));
	$this->addFrontSystem(new TrekPhaser(3, 0, 0, 330, 90));
	$this->addFrontSystem(new TrekLightPhaser(3, 0, 0, 240, 120));
	$this->addFrontSystem(new SWTractorBeam(2,0,360,1));

	$warpNacelle = new TrekWarpDrive(4, 24, 0, 4); //armor, structure, power usage, impulse output
	$impulseDrive->addThruster($warpNacelle);
	$this->addAftSystem($warpNacelle);
		
	$warpNacelle = new TrekWarpDrive(4, 24, 0, 4); //armor, structure, power usage, impulse output
	$impulseDrive->addThruster($warpNacelle);
	$this->addAftSystem($warpNacelle);
		
	$projection = new TrekShieldProjection(2, 24, 6, 90, 270, 'A');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(1, 6, 2, 2, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addAftSystem($projector);
		$projector = new TrekShieldProjector(1, 6, 2, 2, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addAftSystem($projector);
	$this->addAftSystem($projection);
	$this->addAftSystem(new TrekLightPhaser(3, 0, 0, 0, 240));
	$this->addAftSystem(new TrekLightPhaser(3, 0, 0, 120, 360));

		
	//technical thrusters - unlimited, like for LCVs		
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance  
        $this->addPrimarySystem($impulseDrive);
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 45));
        $this->addPrimarySystem(new Structure( 4, 45));
        // no aft structure, can't fall off and should be safe therefore    
	    
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
                    11 => "Scanner",
                    14 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
				2 => "Shield Projector",
				6 => "Photon Torpedo",
				9 => "Phaser",
				10 => "Light Phaser",
				11 => "Tractor Beam",
				18 => "Structure",
				20 => "Primary",
            ),
            2=> array(
				2 => "Shield Projector",
				4 => "0:Hangar",
				9 => "Nacelle",
				11 => "Phaser",
				12 => "Light Phaser",
				18 => "Structure",
				20 => "Primary",
            ),
       );
	    
	    
    }
}
?>