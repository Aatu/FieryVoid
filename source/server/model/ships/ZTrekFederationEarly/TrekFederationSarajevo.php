<?php
class TrekFederationSarajevo extends MediumShip{
	
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 180;
        $this->faction = "ZStarTrek Federation (early)";
        $this->phpclass = "TrekFederationSarajevo";
        $this->imagePath = "img/ships/StarTrek/Sarajevo.png";
        $this->shipClass = "Sarajevo Transport";

	$this->unofficial = true;
        $this->canvasSize = 100;
	$this->isd = 2150;
	
		$this->notes = "Military transport.";

	$this->fighters = array("Shuttlecraft"=>4);
		$this->customFighter = array("Human small craft"=>4); //can deploy small craft with Human crew
        
        $this->forwardDefense = 12;
        $this->sideDefense = 14;

        $this->gravitic = true;    
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 8 *5; //deliberately lowered compared to standard MCV

        $this->addPrimarySystem(new Reactor(3, 8, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 4, 3));
        $this->addPrimarySystem(new Hangar(3, 4));
		$this->addPrimarySystem(new CargoBay(2, 30));
		$impulseDrive = new TrekImpulseDrive(3,15,0,1,3);

		$projection = new TrekShieldProjection(1, 6, 3, 270, 90, 'F');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
		$projection->displayName = "Polarized Hull Plating";
		$this->addFrontSystem($projection);
		
		$this->addFrontSystem(new TrekLtPhaseCannon(2, 4, 2, 270, 30));
		$this->addFrontSystem(new TrekLtPhaseCannon(2, 4, 2, 330, 90));
	    
		$warpNacelle = new TrekWarpDrive(3, 12, 0, 4); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
		$warpNacelle = new TrekWarpDrive(3, 12, 0, 4); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);

			
		$projection = new TrekShieldProjection(1, 6, 3, 90, 270, 'A');//parameters: $armor, $maxhealth, $rating, $arc from/to - F/A/L/R suggests whether to use left or right graphics
			$projection->displayName = "Polarized Hull Plating";
		$this->addAftSystem($projection);
		
		$this->addAftSystem(new TrekLtPhaseCannon(2, 4, 2, 210, 360));
		$this->addAftSystem(new TrekLtPhaseCannon(2, 4, 2, 0, 150));

		//technical thrusters - unlimited, like for LCVs		
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance   
        $this->addPrimarySystem($impulseDrive);

        $this->addPrimarySystem(new Structure(3, 40));


	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			5 => "Scanner",
			8 => "Hangar",
			13 => "Cargo Bay",
			16 => "Engine",
			19 => "Reactor",
			20 => "C&C",
		),
		1=> array(
		    4 => "Light Phase Cannon",
			8 => "0:Cargo Bay",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
		    5 => "Nacelle",
			8 => "Light Phase Cannon",
			10 => "0:Cargo Bay",
			17 => "Structure",
			20 => "Primary",
		),
	);

        
        }
    }
?>