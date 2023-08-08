<?php
class TellariteCruiser extends HeavyCombatVessel{
	
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 325;
	$this->faction = "ZStarTrek Federation (early)";
        $this->phpclass = "TellariteCruiser";
        $this->imagePath = "img/ships/StarTrek/TellariteCruiser.png";
        $this->shipClass = "Tellarite Cruiser";

	$this->unofficial = true;
	$this->isd = 2145;


	$this->fighters = array("Shuttlecraft"=>3);
		$this->customFighter = array("Tellarite small craft"=>3); //can deploy small craft with Tellarite crew
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
 
        $this->gravitic = true;       
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
	$this->iniativebonus = 7 *5; 
			
		$this->addPrimarySystem(new CnC(4, 10, 0, 0));
			$this->addPrimarySystem(new Reactor(4, 16, 0, 2));
			$this->addPrimarySystem(new Scanner(3, 12, 6, 4));
		$this->addPrimarySystem(new Hangar(3, 6, 3));

  
		$projection = new TrekShieldProjection(1, 12, 5, 270, 90, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(1, 6, 1, 1, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addFrontSystem($projector);
			$projector = new TrekShieldProjector(1, 6, 1, 1, 270, 90, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addFrontSystem($projector);
		$this->addFrontSystem($projection);
        $this->addFrontSystem(new ParticleCutter(4, 8, 3, 300, 60));
		$this->addFrontSystem(new CustomEarlyLtParticleCutter(2, 0, 0, 240, 60));
		$this->addFrontSystem(new CustomEarlyLtParticleCutter(2, 0, 0, 270, 90));
		$this->addFrontSystem(new CustomEarlyLtParticleCutter(2, 0, 0, 300, 120));


		$impulseDrive = new TrekImpulseDrive(3,20,0,0,2); //Impulse Drive is an engine in its own right, in addition to serving as hub for Nacelle output: $armour, $maxhealth, $powerReq, $output, $boostEfficiency
			$warpNacelle = new TrekWarpDrive(2, 10, 0, 2); //armor, structure, power usage, impulse output
			$impulseDrive->addThruster($warpNacelle);
			$this->addAftSystem($warpNacelle);
			$warpNacelle = new TrekWarpDrive(2, 10, 0, 2); //armor, structure, power usage, impulse output
			$impulseDrive->addThruster($warpNacelle);
			$this->addAftSystem($warpNacelle);
			$warpNacelle = new TrekWarpDrive(2, 10, 0, 2); //armor, structure, power usage, impulse output
			$impulseDrive->addThruster($warpNacelle);
			$this->addAftSystem($warpNacelle);
			$warpNacelle = new TrekWarpDrive(2, 10, 0, 2); //armor, structure, power usage, impulse output
			$impulseDrive->addThruster($warpNacelle);
			$this->addAftSystem($warpNacelle); 
        $this->addPrimarySystem($impulseDrive);

		
	$projection = new TrekShieldProjection(1, 12, 5, 90, 270, 'A');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(1, 6, 1, 1, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addAftSystem($projector);
		$projector = new TrekShieldProjector(1, 6, 1, 1, 90, 270, 'A'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addAftSystem($projector);
	$this->addAftSystem($projection);
	$this->addAftSystem(new CustomEarlyLtParticleCutter(2, 0, 0, 0, 240));
	$this->addAftSystem(new CustomEarlyLtParticleCutter(2, 0, 0, 120, 360));

		
		//technical thrusters - unlimited, like for LCVs	
		$this->addAftSystem(new InvulnerableThruster(99, 1, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 1, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 1, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 1, 0, 99, 4)); //unhitable and with unlimited thrust allowance 
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 40));
        $this->addPrimarySystem(new Structure( 4, 36));
        // no aft structure, can't fall off and should be safe therefore    
	    
        $this->hitChart = array(
            0=> array(
				8 => "Structure",
				11 => "Scanner",
				14 => "Engine",
				16 => "Hangar",
				18 => "Reactor",
				20 => "C&C",
            ),
            1=> array(
				2 => "Shield Projector",
				5 => "Particle Cutter",
				10 => "Light Phase Cannon",
				17 => "Structure",
				20 => "Primary",
            ),
            2=> array(
				2 => "Shield Projector",
				8 => "Nacelle",
				11 => "Light Phase Cannon",
				17 => "Structure",
				20 => "Primary",
            ),
       );
	    
	    
    }
}
?>