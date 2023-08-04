<?php
class CraytanCorvette2126 extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 350;
        $this->faction = "ZNexus Craytan";
        $this->phpclass = "CraytanCorvette2126";
        $this->imagePath = "img/ships/Nexus/CraytanCorvette.png";
        $this->shipClass = "Corvette (2126 refit)";
			$this->variantOf = "Corvette";
			$this->occurence = "common";
		$this->unofficial = true;
        $this->canvasSize = 60;
        $this->agile = true;
	    $this->isd = 2126;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 12;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(4, 9, 0, 0));
        $this->addPrimarySystem(new CnC(4, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 4, 6));
        $this->addPrimarySystem(new Engine(3, 11, 0, 10, 3));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 5, 4));        
        $this->addPrimarySystem(new CargoBay(4, 9));
        
		$this->addFrontSystem(new MediumPlasma(2, 5, 3, 300, 60));
		$this->addFrontSystem(new NexusACIDS(2, 6, 2, 240, 120));
		$this->addFrontSystem(new MediumPlasma(2, 5, 3, 300, 60));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
	    
		$this->addAftSystem(new LightPlasma(2, 4, 2, 180, 360));
		$this->addAftSystem(new NexusACIDS(2, 6, 2, 90, 270));
		$this->addAftSystem(new LightPlasma(2, 4, 2, 0, 180));
        $this->addAftSystem(new Thruster(3, 10, 0, 5, 2));    
        $this->addAftSystem(new Thruster(3, 10, 0, 5, 2));    
       
        $this->addPrimarySystem(new Structure(5, 40));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			8 => "Thruster",
			11 => "Cargo Bay",
			14 => "Scanner",
			17 => "Engine",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			6 => "Thruster",
			8 => "Medium Plasma Cannon",
			9 => "Advanced Close-In Defense System",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Thruster",
			8 => "Light Plasma Cannon",
			9 => "Advanced Close-In Defense System",
			10 => "Hangar",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
