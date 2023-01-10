<?php
class TrekFederationJClass extends MediumShip{
	
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 100;
        $this->faction = "ZStarTrek Federation (early)";
        $this->phpclass = "TrekFederationJClass";
        $this->imagePath = "img/ships/StarTrek/JClassFreighter.png";
        $this->shipClass = "J-Class Freighter";

	$this->unofficial = true;
        $this->canvasSize = 100;
	$this->isd = 2125;

		$this->notes = "Civilian freighter.";
		
	$this->fighters = array("Shuttlecraft"=>1);
		$this->customFighter = array("Human small craft"=>1); //can deploy small craft with Human crew
        
        $this->forwardDefense = 12;
        $this->sideDefense = 16;

        $this->gravitic = true;    
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 2 *5; //deliberately lowered compared to standard MCV


		$cA = new CargoBay(2, 20);
		$cB = new CargoBay(2, 20);
		$cC = new CargoBay(2, 20);
		$cD = new CargoBay(2, 20);
		$cE = new CargoBay(2, 20);

		
		$cA->displayName = "Cargo Container A";
		$cB->displayName = "Cargo Container B";
		$cC->displayName = "Cargo Container C";
		$cD->displayName = "Cargo Container D";
		$cE->displayName = "Cargo Container E";

        $this->addPrimarySystem(new Reactor(3, 6, 0, 2));
        $this->addPrimarySystem(new CnC(3, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 6, 3, 2));
	$impulseDrive = new TrekImpulseDrive(3,15,0,0,2);
        $this->addPrimarySystem(new Hangar(2, 1));
	$this->addPrimarySystem(new TrekPlasmaBurst(2, 4, 2, 0, 360));

        $this->addFrontSystem($cA);
        $this->addFrontSystem($cB);
        $this->addFrontSystem($cC);
	    
		$warpNacelle = new TrekWarpDrive(2, 8, 0, 3); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
		$warpNacelle = new TrekWarpDrive(2, 8, 0, 3); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);

        $this->addAftSystem($cD);
        $this->addAftSystem($cE);

		//technical thrusters - unlimited, like for LCVs		
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance   
        $this->addPrimarySystem($impulseDrive);

        $this->addPrimarySystem(new Structure(2, 30));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			6 => "Scanner",
			7 => "Plasma Burst",
			8 => "Hangar",
			14 => "Engine",
			17 => "Reactor",
			20 => "C&C",
		),
		1=> array(
        		4 => "Cargo Container A",
        		8 => "Cargo Container B",
        		12 => "Cargo Container C",
			13 => "0:Plasma Burst",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
		    4 => "Nacelle",
        		8 => "Cargo Container E",
        		12 => "Cargo Container F",
			13 => "0:Plasma Burst",
			17 => "Structure",
			20 => "Primary",
		),
	);

        
        }
    }
?>