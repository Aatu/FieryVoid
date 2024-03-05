<?php
class MakarTorgeth extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 220;
        $this->faction = "ZNexus Makar Federation";
        $this->phpclass = "MakarTorgeth";
        $this->imagePath = "img/ships/Nexus/makarRatash_v3.png";
        $this->shipClass = "Torgeth Troop Transport";
			$this->variantOf = "Ratash Early Frigate";
			$this->occurence = "uncommon";
		$this->unofficial = true;
        $this->canvasSize = 80;
        $this->agile = true;
	    $this->isd = 1912;

	    $this->notes = 'Atmospheric capable';
        
        $this->forwardDefense = 11;
        $this->sideDefense = 12;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(4, 14, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 3, 5));
        $this->addPrimarySystem(new Engine(4, 16, 0, 6, 3));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 4, 4));   
        
		$this->addFrontSystem(new EWRocketLauncher(3, 4, 1, 240, 60));
		$this->addFrontSystem(new Quarters(3, 9));
		$this->addFrontSystem(new EWRocketLauncher(3, 4, 1, 300, 120));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
	    
		$this->addAftSystem(new NexusDefenseGun(3, 4, 1, 180, 360));
		$this->addAftSystem(new NexusDefenseGun(3, 4, 1, 0, 180));
        $this->addAftSystem(new Thruster(3, 20, 0, 6, 2));    
        
        $this->addPrimarySystem(new Structure(4, 50));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			10 => "Thruster",
			14 => "Scanner",
			17 => "Engine",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			5 => "Thruster",
			7 => "Quarters",
			9 => "Rocket Launcher",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Thruster",
			8 => "Defense Gun",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
