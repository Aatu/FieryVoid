<?php
class VulcanVahklas extends LCV{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 290;
        $this->faction = "ZStarTrek Federation (early)";
        $this->phpclass = "VulcanVahklas";
        $this->imagePath = "img/ships/StarTrek/VulcanVahklas.png";
        $this->shipClass = "Vulcan Vah'Klas Frigate";
		
		$this->notes = "Does not require hangar.";

	$this->unofficial = true;
        $this->canvasSize = 100;
	$this->isd = 2140;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;

        $this->gravitic = true;    
        $this->turncost = 0.33;
        $this->turndelaycost = 0.25;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 14 *5;
		$this->hangarRequired = ''; //no hangar required!


		$this->addFrontSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance

		$this->addPrimarySystem(new Reactor(3, 10, 0, 2));
		$this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
    	$sensors = new Scanner(3, 12, 4, 4);
			//$sensors->markLCV();
			$this->addPrimarySystem($sensors);
	$impulseDrive = new TrekImpulseDrive(3,16,0,2,2);

	$projection = new TrekShieldProjection(2, 12, 5, 0, 360, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projector = new TrekShieldProjector(2, 6, 2, 2, 0, 360, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->addProjector($projector);
		$this->addPrimarySystem($projector);
	$this->addPrimarySystem($projection);
		$this->addFrontSystem(new TrekPhaseCannon(2, 6, 4, 300, 60));
		$this->addFrontSystem(new TrekPhaseCannon(2, 6, 4, 300, 60));
		$this->addFrontSystem(new TrekLtPhaseCannon(2, 4, 2, 180, 30));
		$this->addFrontSystem(new TrekLtPhaseCannon(2, 4, 2, 330, 180));
	

		$warpNacelle = new TrekWarpDrive(3, 12, 0, 4); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
        $this->addPrimarySystem($impulseDrive);

        $this->addPrimarySystem(new Structure(3, 40));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			7 => "Structure",
			10 => "1:Phase Cannon",
			12 => "1:Light Phase Cannon",
			14 => "0:Shield Projector",
			17 => "2:Nacelle",
			18 => "0:Engine",
			19 => "0:Reactor",
			20 => "0:Scanner",
		),

		1=> array(
			8 => "Structure",
			10 => "1:Phase Cannon",
			12 => "1:Light Phase Cannon",
			14 => "0:Shield Projector",
			17 => "2:Nacelle",
			18 => "0:Engine",
			19 => "0:Reactor",
			20 => "0:Scanner",
		),

		2=> array(
			8 => "Structure",
			10 => "1:Phase Cannon",
			12 => "1:Light Phase Cannon",
			14 => "0:Shield Projector",
			17 => "2:Nacelle",
			18 => "0:Engine",
			19 => "0:Reactor",
			20 => "0:Scanner",
		),		

	);

        
        }
    }
?>
