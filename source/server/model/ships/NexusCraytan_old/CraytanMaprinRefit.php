<?php
class CraytanMaprinRefit extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 310;
        $this->faction = "Nexus Craytan Union (early)";
        $this->phpclass = "CraytanMaprinRefit";
        $this->imagePath = "img/ships/Nexus/craytan_corvette.png";
        $this->shipClass = "Maprin Corvette (2087)";
			$this->variantOf = "Maprin Corvette";
			$this->occurence = "common";
		$this->unofficial = true;
        $this->canvasSize = 75;
        $this->agile = true;
	    $this->isd = 2087;

        $this->fighters = array("assault shuttles"=>2);
	    $this->notes = 'Atmospheric capable';
        
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
        $this->addPrimarySystem(new Engine(3, 11, 0, 8, 3));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 4));        
        $this->addPrimarySystem(new Magazine(4, 9));
        
		$this->addFrontSystem(new MediumPlasma(2, 5, 3, 300, 60));
		$this->addFrontSystem(new NexusCIDS(2, 4, 2, 240, 120));
		$this->addFrontSystem(new MediumPlasma(2, 5, 3, 300, 60));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
	    
		$this->addAftSystem(new NexusLightSentryGun(2, 5, 1, 180, 360));
		$this->addAftSystem(new NexusCIDS(2, 4, 2, 60, 300));
		$this->addAftSystem(new NexusLightSentryGun(2, 5, 1, 0, 180));
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));    
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));    
		$this->addAftSystem(new Hangar(2, 2));
       
        $this->addPrimarySystem(new Structure(3, 40));

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
			8 => "Medium Plasma Cannon",
			10 => "Close-In Defense System",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Thruster",
			8 => "Light Sentry Gun",
			10 => "Close-In Defense System",
			12 => "Hangar",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
