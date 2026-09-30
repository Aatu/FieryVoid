<?php
class VelraxReshithAuxCarrier extends MediumShipLeftRight{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 150;
        $this->faction = "Nexus Velrax Republic (early)";
        $this->phpclass = "VelraxReshithAuxCarrier";
        $this->imagePath = "img/ships/Nexus/velraxKrissith.png";
        $this->shipClass = "Reshith Auxiliary Carrier";
		$this->unofficial = true;
        $this->canvasSize = 100;
	    $this->isd = 2013;
		$this->limited = 10;
		
        $this->fighters = array("light"=>12);

        $this->forwardDefense = 13;
        $this->sideDefense = 13;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 999;
        $this->pivotcost = 999;
        $this->iniativebonus = -15;
         
        $this->addPrimarySystem(new Reactor(2, 4, 0, 0));
        $this->addPrimarySystem(new CnC(2, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(2, 4, 2, 2));
        $this->addPrimarySystem(new Engine(2, 6, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(2, 12, 0, 4, 1));
        $this->addPrimarySystem(new Thruster(2, 12, 0, 4, 2));        
		$this->addPrimarySystem(new Hangar(0, 4, 2));
		
		$this->addLeftSystem(new NexusIonGun(1, 2, 2, 180, 60));
        $this->addLeftSystem(new Thruster(2, 12, 0, 4, 3));
		$this->addLeftSystem(new FighterRail(3, 6, 6, 0, 'normal'));
	    
		$this->addRightSystem(new NexusIonGun(1, 2, 2, 300, 180));
        $this->addRightSystem(new Thruster(2, 12, 0, 4, 4));    
		$this->addRightSystem(new FighterRail(3, 6, 6, 0, 'normal'));
       
        $this->addPrimarySystem(new Structure(3, 40));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			9 => "Thruster",
			12 => "Scanner",
			15 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),

			3=> array(
				6 => "Thruster",
				9 => "Ion Gun",
				17 => "Structure",
				20 => "Primary",
			),

			4=> array(
				6 => "Thruster",
				9 => "Ion Gun",
   				7 => "Cargo Bay D",
   				9 => "Cargo Bay E",
				11=> "Cargo Bay F",
				17 => "Structure",
				20 => "Primary",
		),

	);

        
        }
    }
?>
