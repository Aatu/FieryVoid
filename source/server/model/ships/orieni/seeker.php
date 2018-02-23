<?php
class Seeker extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 400;
	$this->faction = "Orieni";
        $this->phpclass = "Seeker";
        $this->imagePath = "img/ships/seeker.png";
        	$this->canvasSize = 100;
        $this->shipClass = "Seeker Recon Corvette";
        $this->variantOf = "Steadfast Escort Corvette";
        	$this->occurence = "rare";
	    $this->isd = 2007;
	    
        $this->agile = true;        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
	$this->iniativebonus = 60;
        


        $this->addPrimarySystem(new Reactor(4, 13, 0, 0));
        $this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new ElintScanner(3, 12, 4, 4));
        $this->addPrimarySystem(new Engine(3, 18, 0, 12, 2));
	$this->addPrimarySystem(new Hangar(1, 1));
	$this->addPrimarySystem(new Thruster(2, 10, 0, 5, 3));
	$this->addPrimarySystem(new Thruster(2, 10, 0, 5, 4));        
		

        $this->addFrontSystem(new Thruster(1, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(1, 6, 0, 2, 1));
        $this->addFrontSystem(new GaussCannon(2, 10, 4, 300, 60));
        $this->addFrontSystem(new GaussCannon(2, 10, 4, 300, 60));

        $this->addAftSystem(new Thruster(1, 8, 0, 4, 2));
        $this->addAftSystem(new Thruster(2, 8, 0, 4, 2));
        $this->addAftSystem(new Thruster(1, 8, 0, 4, 2));
        $this->addAftSystem(new RapidGatling(2, 4, 1, 120, 360));
        $this->addAftSystem(new RapidGatling(2, 4, 1, 0, 240));    
        
       
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
			9 => "Gauss Cannon",
			17 => "Structure",
			20 => "Primary",
		),
		2=> array( //Aft
			7 => "Thruster",
			9 => "Rapid Gatling Railgun",
			17 => "Structure",
			20 => "Primary",
		),
	);
			
		
        }
    }
?>
