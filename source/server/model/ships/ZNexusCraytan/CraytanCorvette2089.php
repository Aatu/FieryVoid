<?php
class CraytanCorvette2089 extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 350;
        $this->faction = "ZNexus Craytan Union";
        $this->phpclass = "CraytanCorvette2089";
        $this->imagePath = "img/ships/Nexus/CraytanCorvette.png";
        $this->shipClass = "Corvette (2089 refit)";
			$this->variantOf = "Corvette";
			$this->occurence = "common";
		$this->unofficial = true;
        $this->canvasSize = 60;
        $this->agile = true;
	    $this->isd = 2089;
        
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
        $this->addPrimarySystem(new Scanner(3, 12, 4, 5));
        $this->addPrimarySystem(new Engine(3, 11, 0, 8, 3));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 4));        
        $this->addPrimarySystem(new Magazine(4, 9));
        
		$this->addFrontSystem(new NexusHeavyAutocannon(2, 6, 2, 300, 60));
		$this->addFrontSystem(new NexusACIDS(2, 6, 2, 240, 120));
		$this->addFrontSystem(new NexusHeavyAutocannon(2, 6, 2, 300, 60));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
	    
		$this->addAftSystem(new LightPlasma(2, 4, 2, 180, 360));
		$this->addAftSystem(new NexusACIDS(2, 6, 2, 90, 270));
		$this->addAftSystem(new LightPlasma(2, 4, 2, 0, 180));
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));    
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));    
       
        $this->addPrimarySystem(new Structure(4, 40));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			8 => "Thruster",
			11 => "Magazine",
			14 => "Scanner",
			17 => "Engine",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			6 => "Thruster",
			8 => "Heavy Autocannon",
			10 => "Advanced Close-In Defense System",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Thruster",
			8 => "Light Plasma Cannon",
			10 => "Advanced Close-In Defense System",
			11 => "Hangar",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
