<?php
class AndorianKumari extends HeavyCombatVesselLeftRight{
	
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 480;
	$this->faction = "ZTrek Playtest Federation";
        $this->phpclass = "AndorianKumari";
        $this->imagePath = "img/ships/StarTrek/AndorianCruiser.png";
        $this->shipClass = "Andorian Kumari Cruiser";

	$this->unofficial = true;
	    $this->isd = '2145';

	$this->fighters = array("Shuttlecraft"=>3);

        $this->forwardDefense = 13;
        $this->sideDefense = 13;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 35;

	$this->addPrimarySystem(new CnC(4, 15, 0, 0));
        $this->addPrimarySystem(new Reactor(4, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 14, 6, 5));
	$this->addPrimarySystem(new Hangar(3, 3));
	$this->addPrimarySystem(new SWTractorBeam(2,0,360,1));
	$impulseDrive = new TrekImpulseDrive(4,20,0,2,3); //Impulse Drive is an engine in its own right, in addition to serving as hub for Nacelle output: $armour, $maxhealth, $powerReq, $output, $boostEfficiency
		

	$projection = new TrekShieldProjection(2, 15, 5, 180, 300, 'L');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(1, 6, 2, 2, 180, 300, 'L'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addLeftSystem($projector);
		$projector = new TrekShieldProjector(1, 6, 2, 2, 180, 300, 'L'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addLeftSystem($projector);
	$this->addLeftSystem($projection); 
        $this->addLeftSystem(new ParticleCannon(4, 8, 6, 240, 60));
        $this->addLeftSystem(new LightParticleCannon(2, 6, 4, 180, 360));
        $this->addLeftSystem(new StdParticleBeam(3, 4, 2, 120, 360));
	$warpNacelle = new TrekWarpDrive(3, 20, 0, 4); //armor, structure, power usage, impulse output
	$impulseDrive->addThruster($warpNacelle);
	$this->addLeftSystem($warpNacelle);

	$projection = new TrekShieldProjection(2, 15, 5, 60, 180, 'R');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(1, 6, 2, 2, 60, 180, 'R'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addRightSystem($projector);
		$projector = new TrekShieldProjector(1, 6, 2, 2, 60, 180, 'R'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addRightSystem($projector);
	$this->addRightSystem($projection);
        $this->addRightSystem(new ParticleCannon(4, 8, 6, 300, 120));
        $this->addRightSystem(new LightParticleCannon(2, 6, 4, 0, 180));
        $this->addRightSystem(new StdParticleBeam(3, 4, 2, 0, 240));
	$warpNacelle = new TrekWarpDrive(3, 20, 0, 4); //armor, structure, power usage, impulse output
	$impulseDrive->addThruster($warpNacelle);
	$this->addRightSystem($warpNacelle);

	//technical thrusters - unlimited, like for LCVs		
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
	$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance  
        $this->addPrimarySystem($impulseDrive);

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(4, 36));
        $this->addLeftSystem(new Structure(4, 40));
        $this->addRightSystem(new Structure(4, 40));
    
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
            3=> array(
				2 => "Shield Projector",
				6 => "Nacelle",
				8 => "Particle Cannon",
				9 => "Light Particle Cannon",
				11 => "Standard Particle Beam",
				18 => "Structure",
				20 => "Primary",
            ),
            4=> array(
				2 => "Shield Projector",
				6 => "Nacelle",
				8 => "Particle Cannon",
				9 => "Light Particle Cannon",
				11 => "Standard Particle Beam",
				18 => "Structure",
				20 => "Primary",
        		),
        );
    
    }
}
?>