<?php
class SalbezPassengerLinerRefit extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 40;
        $this->faction = "Nexus Support Units";
        $this->phpclass = "SalbezPassengerLinerRefit";
        $this->imagePath = "img/ships/Nexus/salbez_drevnan3.png";
        $this->shipClass = "Sal-bez Passenger Liner (2102)";
			$this->variantOf = "Sal-bez Passenger Liner";
			$this->occurence = "common";
		$this->unofficial = true;
        $this->canvasSize = 90;
	    $this->isd = 2001;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 13;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
//        $this->rollcost = 2;
//        $this->pivotcost = 2;
        $this->iniativebonus = 10;
         
        $this->addPrimarySystem(new Reactor(2, 8, 0, 0));
        $this->addPrimarySystem(new CnC(3, 5, 0, 0));
        $this->addPrimarySystem(new Scanner(1, 6, 1, 3));
        $this->addPrimarySystem(new Engine(2, 9, 0, 6, 4));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 360));
        $this->addPrimarySystem(new Thruster(2, 8, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(2, 8, 0, 3, 4));        
        $this->addPrimarySystem(new Hangar(0, 4));
        
		$this->addFrontSystem(new Quarters(1, 6));
		$this->addFrontSystem(new Quarters(1, 6));
		$this->addFrontSystem(new Quarters(1, 6));
        $this->addFrontSystem(new Thruster(2, 6, 0, 4, 1));
	    
		$this->addAftSystem(new Quarters(1, 6));
		$this->addAftSystem(new Quarters(1, 6));
		$this->addAftSystem(new Quarters(1, 6));
        $this->addAftSystem(new Thruster(2, 6, 0, 3, 2));    
        $this->addAftSystem(new Thruster(2, 6, 0, 3, 2));    
       
        $this->addPrimarySystem(new Structure(3, 36));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			7 => "Thruster",
			9 => "Light Particle Beam",
			12 => "Scanner",
			15 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			5 => "Thruster",
			10 => "Quarters",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			5 => "Thruster",
			10 => "Quarters",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
