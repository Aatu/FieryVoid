<?php
class seekerGC extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 440;
	$this->faction = "Great Crusade Orieni Imperium";
        $this->phpclass = "seekerGC";
        $this->imagePath = "img/ships/GCseeker.png";
       	$this->canvasSize = 110;
        $this->shipClass = "Seeker Recon Corvette (2212)";
			$this->variantOf = "Vengeance Attack Frigate";
        	$this->occurence = "rare";
	    $this->isd = 2212;
	    $this->isCombatUnit = false; //not a combat unit, it will never be present in a regular battlegroup
	    //design for exploration groups
	    
        $this->agile = true;        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;

		$this->unofficial = true;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
		$this->iniativebonus = 60;

        $this->addPrimarySystem(new Reactor(4, 13, 0, 0));
        $this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new ElintScanner(3, 12, 4, 6));
        $this->addPrimarySystem(new Engine(3, 18, 0, 12, 2));
		$this->addPrimarySystem(new Hangar(1, 1, 1));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 5, 3));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 5, 4));        

        $this->addFrontSystem(new Thruster(1, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(1, 6, 0, 2, 1));
        $this->addFrontSystem(new GaussRifle(3, 8, 4, 300, 60));
        $this->addFrontSystem(new GaussRifle(3, 8, 4, 300, 60));

        $this->addAftSystem(new Thruster(1, 8, 0, 4, 2));
        $this->addAftSystem(new Thruster(2, 8, 0, 4, 2));
        $this->addAftSystem(new Thruster(1, 8, 0, 4, 2));
        $this->addAftSystem(new ImpRapidGatling(2, 4, 2, 120, 360));
        $this->addAftSystem(new ImpRapidGatling(2, 4, 2, 0, 240));    
       
        $this->addPrimarySystem(new Structure(4, 46));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array( //PRIMARY
			8 => "Thruster",
			11 => "Elint Scanner",
			15 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),
		1=> array( //Front
			6 => "Thruster",
			9 => "Gauss Rifle",
			17 => "Structure",
			20 => "Primary",
		),
		2=> array( //Aft
			7 => "Thruster",
			9 => "Improved Gatling Railgun",
			17 => "Structure",
			20 => "Primary",
		),
	);
			
		
        }
    }
?>
