<?php
class SatyraMCVTest extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 300;
        $this->faction = "Custom Ships";
        $this->phpclass = "SatyraMCVTest";
        $this->imagePath = "img/ships/StarTrek/FederationSaladin.png";
        $this->shipClass = "Satyra MCV Test";

		$this->unofficial = true;
        $this->canvasSize = 100;
		$this->isd = 2263;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 12;
 
        //$this->agile = true; //NOT agile after all       
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 1;
        $this->iniativebonus = 6 *5;

        $this->addPrimarySystem(new Reactor(3, 15, 0, -6));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 6, 4));
        $this->addPrimarySystem(new Hangar(3, 1));
		$this->addPrimarySystem(new Engine(4, 18, 0, 7, 3));
		//Satyra ships are resistent to EM and laser weapons - reflected by this "shield" system
		$armor = new SatyraShield(0,1,0,2,0,360); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
			$armor->displayName = 'Durable Hull';
			$this->addPrimarySystem($armor);

		$this->addFrontSystem(new MolecularDisruptor(3, 10, 8, 330, 30));
		$this->addFrontSystem(new LaserArray(2, 10, 7, 270, 90));

		//technical thrusters - unlimited, like for LCVs		
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance   

        $this->addPrimarySystem(new Structure(4, 48));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			12 => "Structure",
			14 => "1:Molecular Disruptor",
			15 => "1: Laser Array",
			16 => "0:Hangar",
			17 => "0:Sensors",
			18 => "0:Reactor",
			19 => "0:Engine",
			20 => "0:C&C",
		),

		1=> array(
			12 => "Structure",
			14 => "1:Molecular Disruptor",
			15 => "1:Laser Array",
			16 => "0:Hangar",
			17 => "0:Sensors",
			18 => "0:Reactor",
			19 => "0:Engine",
			20 => "0:C&C",
		),

		2=> array(
			12 => "Structure",
			14 => "1:Molecular Disruptor",
			15 => "1:Laser Array",
			16 => "0:Hangar",
			17 => "0:Sensors",
			18 => "0:Reactor",
			19 => "0:Engine",
			20 => "0:C&C",
		),

	);

        
        }
    }
?>