<?php
class TellariteAttackVessel extends LCV{
	
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 180;
		$this->faction = "ZStarTrek Federation (early)";
        $this->phpclass = "TellariteAttackVessel";
        $this->imagePath = "img/ships/StarTrek/TellariteAttackVessel.png";
        $this->shipClass = "Tellarite Attack Vessel";

		$this->notes = "Does not require hangar.";
		
		$this->unofficial = true;
        $this->canvasSize = 100;
		$this->isd = 2142;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 12;

        $this->agile = true;
        $this->gravitic = true;    
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus = 12 *5;
		$this->hangarRequired = ''; //no hangar required!


		$this->addAftSystem(new InvulnerableThruster(99, 1, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 1, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 1, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 1, 0, 99, 4)); //unhitable and with unlimited thrust allowance

		$this->addPrimarySystem(new Reactor(3, 10, 0, 2));
		$this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
    	$sensors = new Scanner(3, 12, 4, 3);
		//$sensors->markLCV(); 
		$this->addPrimarySystem($sensors);

		$projection = new TrekShieldProjection(1, 10, 5, 0, 360, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projector = new TrekShieldProjector(2, 6, 1, 1, 0, 360, 'F'); //parameters: $armor, $maxhealth, $power used, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->addProjector($projector);
			$this->addPrimarySystem($projector);
		$this->addPrimarySystem($projection);

		$this->addFrontSystem(new CustomEarlyLtParticleCutter(2, 0, 0, 210, 30));
		$this->addFrontSystem(new CustomEarlyLtParticleCutter(2, 0, 0, 270, 90));
		$this->addFrontSystem(new CustomEarlyLtParticleCutter(2, 0, 0, 330, 150));
	

		$impulseDrive = new TrekImpulseDrive(3,14,0,0,2);
			$warpNacelle = new TrekWarpDrive(2, 10, 0, 3); //armor, structure, power usage, impulse output
			$impulseDrive->addThruster($warpNacelle);
			$this->addAftSystem($warpNacelle);
			$warpNacelle = new TrekWarpDrive(2, 10, 0, 3); //armor, structure, power usage, impulse output
			$impulseDrive->addThruster($warpNacelle);
			$this->addAftSystem($warpNacelle);
        $this->addPrimarySystem($impulseDrive);

        $this->addPrimarySystem(new Structure(2, 33));

		//d20 hit chart
		$this->hitChart = array(
			
			0=> array(
				8 => "Structure",
				11 => "1:Light Phase Cannon",
				13 => "0:Shield Projector",
				17 => "2:Nacelle",
				18 => "0:Engine",
				19 => "0:Reactor",
				20 => "0:Scanner",
			),

			1=> array(
				8 => "Structure",
				11 => "1:Light Phase Cannon",
				13 => "0:Shield Projector",
				17 => "2:Nacelle",
				18 => "0:Engine",
				19 => "0:Reactor",
				20 => "0:Scanner",
			),

			2=> array(
				8 => "Structure",
				11 => "1:Light Phase Cannon",
				13 => "0:Shield Projector",
				17 => "2:Nacelle",
				18 => "0:Engine",
				19 => "0:Reactor",
				20 => "0:Scanner",
			),		

		);

        
	}
}
?>