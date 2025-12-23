<?php
class MakarPoroke extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 210;
        $this->faction = "Nexus Makar Federation (early)";
        $this->phpclass = "MakarPoroke";
        $this->imagePath = "img/ships/Nexus/makar_ratash2.png";
        $this->shipClass = "Poroke Escort Frigate";
			$this->variantOf = "Ratash Early Frigate";
			$this->occurence = "common";
		$this->unofficial = true;
        $this->canvasSize = 90;
//        $this->agile = true;
	    $this->isd = 1920;

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
        
		$this->addFrontSystem(new NexusDefenseGun(3, 4, 1, 240, 60));
		$this->addFrontSystem(new NexusDefenseGun(3, 4, 1, 240, 60));
		$this->addFrontSystem(new NexusDefenseGun(3, 4, 1, 300, 120));
		$this->addFrontSystem(new NexusDefenseGun(3, 4, 1, 300, 120));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
	    
		$this->addAftSystem(new NexusDefenseGun(3, 4, 1, 180, 360));
		$this->addAftSystem(new PlasmaTorch(1, 4, 2, 180, 360));
		$this->addAftSystem(new PlasmaTorch(1, 4, 2, 0, 180));
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
			6 => "Thruster",
			9 => "Defense Gun",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Thruster",
			8 => "Defense Gun",
			10 => "Plasma Torch",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
