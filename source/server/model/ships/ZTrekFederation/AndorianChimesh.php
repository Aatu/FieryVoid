<?php
class AndorianChimesh extends BaseShipNoAft{
	
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 575;
	$this->faction = "ZTrek Playtest Federation";
        $this->phpclass = "AndorianChimesh";
        $this->imagePath = "img/ships/StarTrek/AndorianChimesh.png";
        $this->shipClass = "Andorian Chimesh Battlecarrier";

	$this->unofficial = true;
	    $this->isd = '2155';


	$this->fighters = array("medium"=>24);
        
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
 
        $this->gravitic = true;       
        $this->turncost = 1;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 4;
	$this->iniativebonus = 0; 
		
	$this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new Reactor(4, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 5, 5));
	$this->addPrimarySystem(new Hangar(3, 6));
	$this->addPrimarySystem(new SWTractorBeam(2,0,360,1));

	$impulseDrive = new TrekImpulseDrive(4,20,0,2,3); //Impulse Drive is an engine in its own right, in addition to serving as hub for Nacelle output: $armour, $maxhealth, $powerReq, $output, $boostEfficiency
		
  
	$projection = new TrekShieldProjection(2, 20, 6, 300, 60, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(1, 6, 2, 2, 300, 60, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addFrontSystem($projector);
		$projector = new TrekShieldProjector(1, 6, 2, 2, 300, 60, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addFrontSystem($projector);
	$this->addFrontSystem($projection);
	$this->addFrontSystem(new Hangar(3, 6));
	$this->addFrontSystem(new Hangar(3, 6));
        $this->addFrontSystem(new ParticleCannon(3, 8, 6, 270, 30));
        $this->addFrontSystem(new ParticleCannon(3, 8, 6, 330, 90));


	$projection = new TrekShieldProjection(2, 16, 6, 180, 300, 'L');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(1, 6, 1, 1, 180, 300, 'L'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addLeftSystem($projector);
		$projector = new TrekShieldProjector(1, 6, 1, 1, 180, 300, 'L'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addLeftSystem($projector);
	$this->addLeftSystem($projection);
	$this->addLeftSystem(new Hangar(3, 6));
        $this->addLeftSystem(new LightParticleCannon(2, 6, 4, 240, 360));
        $this->addLeftSystem(new StdParticleBeam(2, 4, 2, 180, 360));
        $this->addLeftSystem(new StdParticleBeam(2, 4, 2, 180, 360));
	$warpNacelle = new TrekWarpDrive(3, 20, 0, 4); //armor, structure, power usage, impulse output
	$impulseDrive->addThruster($warpNacelle);
	$this->addLeftSystem($warpNacelle);



	$projection = new TrekShieldProjection(2, 16, 6, 60, 180, 'R');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(1, 6, 1, 1, 60, 180, 'R'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addRightSystem($projector);
		$projector = new TrekShieldProjector(1, 6, 1, 1, 60, 180, 'R'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addRightSystem($projector);
	$this->addRightSystem($projection);
	$this->addRightSystem(new Hangar(3, 6));
        $this->addRightSystem(new LightParticleCannon(2, 6, 4, 0, 120));
        $this->addRightSystem(new StdParticleBeam(2, 4, 2, 0, 180));
        $this->addRightSystem(new StdParticleBeam(2, 4, 2, 0, 180));
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
        $this->addFrontSystem(new Structure( 4, 40));
        $this->addLeftSystem(new Structure( 4, 40));
        $this->addRightSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 4, 40));

	    
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
				6 => "Hangar",
				10 => "Particle Cannon",
				18 => "Structure",
				20 => "Primary",
            ),
            3=> array(
				2 => "Shield Projector",
				6 => "Nacelle",
				8 => "Hangar",
				9 => "Light Particle Cannon",
				11 => "Standard Particle Beam",
				18 => "Structure",
				20 => "Primary",
            ),
            4=> array(
				2 => "Shield Projector",
				6 => "Nacelle",
				8 => "Hangar",
				9 => "Light Particle Cannon",
				11 => "Standard Particle Beam",
				18 => "Structure",
				20 => "Primary",
            ),
       );
	    
	    
    }
}
?>