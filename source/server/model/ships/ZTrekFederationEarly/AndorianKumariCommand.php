<?php
class AndorianKumariCommand extends HeavyCombatVesselLeftRight{
	
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 525;
	$this->faction = "ZStarTrek (early) Federation";
        $this->phpclass = "AndorianKumariCommand";
        $this->imagePath = "img/ships/StarTrek/AndorianCruiser.png";
        $this->shipClass = "Andorian Kumari Command Cruiser";

	$this->unofficial = true;
	    $this->isd = '2152';

	$this->fighters = array("medium"=>12, "Shuttlecraft"=>2);
	$this->customFighter = array("Andorian small craft"=>14); //can deploy small craft with Andorian crew

        $this->forwardDefense = 13;
        $this->sideDefense = 13;
        
        $this->occurence = "uncommon";
        $this->variantOf = "Andorian Kumari Cruiser";

        $this->gravitic = true;    
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;

	$this->addPrimarySystem(new CnC(5, 18, 0, 0));
        $this->addPrimarySystem(new Reactor(4, 22, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 15, 6, 6));
	$this->addAftSystem(new StdParticleBeam(2, 4, 2, 60, 300));
	$this->addFrontSystem(new ParticleCannon(4, 8, 6, 300, 60));
	$impulseDrive = new TrekImpulseDrive(4,20,0,2,3); //Impulse Drive is an engine in its own right, in addition to serving as hub for Nacelle output: $armour, $maxhealth, $powerReq, $output, $boostEfficiency
		

	$projection = new TrekShieldProjection(2, 15, 5, 180, 30, 'L');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(1, 6, 2, 2, 180, 30, 'L'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addLeftSystem($projector);
		$projector = new TrekShieldProjector(1, 6, 2, 2, 180, 30, 'L'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addLeftSystem($projector);
	$this->addLeftSystem($projection); 
        $this->addLeftSystem(new ParticleCannon(4, 8, 6, 240, 60));
        $this->addLeftSystem(new StdParticleBeam(2, 4, 2, 180, 30));
        $this->addLeftSystem(new StdParticleBeam(2, 4, 2, 150, 360));
	$this->addLeftSystem(new Hangar(3, 7)); //6 fighters and 1 shuttle
	$warpNacelle = new TrekWarpDrive(3, 20, 0, 4); //armor, structure, power usage, impulse output
	$impulseDrive->addThruster($warpNacelle);
	$this->addLeftSystem($warpNacelle);

	$projection = new TrekShieldProjection(2, 15, 5, 330, 180, 'R');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(1, 6, 2, 2, 330, 180, 'R'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addRightSystem($projector);
		$projector = new TrekShieldProjector(1, 6, 2, 2, 330, 180, 'R'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addRightSystem($projector);
	$this->addRightSystem($projection);
        $this->addRightSystem(new ParticleCannon(4, 8, 6, 300, 120));
        $this->addRightSystem(new StdParticleBeam(2, 4, 2, 330, 180));
        $this->addRightSystem(new StdParticleBeam(2, 4, 2, 0, 210));
        $this->addRightSystem(new Hangar(3, 7));//6 fighters and 1 shuttle
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
				1 => "3:Nacelle",	
				2 => "4:Nacelle",	
				7 => "Structure",
				9 => "1:Particle Cannon",
				11 => "2:Standard Particle Beam",	
				13 => "Hangar",
				15 => "Scanner",
				17 => "Engine",
				19 => "Reactor",
				20 => "C&C",
            ),
            3=> array(
				2 => "Shield Projector",
				5 => "Nacelle",
				7 => "Particle Cannon",
				9 => "Standard Particle Beam",
				12 => "Hangar",
				18 => "Structure",
				20 => "Primary",
            ),
            4=> array(
				2 => "Shield Projector",
				5 => "Nacelle",
				7 => "Particle Cannon",
				9 => "Standard Particle Beam",
				12 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
        );
    
    }
}
?>