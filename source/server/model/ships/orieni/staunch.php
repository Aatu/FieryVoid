<?php
class Staunch extends MediumShip{
    /*Orieni Staunch Strike Frigate, V6*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 400;
	$this->faction = "Orieni";
        $this->phpclass = "Staunch";
        $this->imagePath = "img/ships/steadfast.png";
        $this->shipClass = "Staunch Strike Frigate";
        $this->variantOf = "Steadfast Escort Corvette";
	    $this->isd = 2007;
        $this->agile = true;
        $this->canvasSize = 100;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
	$this->iniativebonus = 60;

        $this->occurence = "uncommon";
         
        $this->addPrimarySystem(new Reactor(4, 15, 0, 3));
        $this->addPrimarySystem(new CnC(4, 15, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 3, 6));
        $this->addPrimarySystem(new Engine(3, 15, 0, 10, 2));
		$this->addPrimarySystem(new Hangar(1, 1));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 5, 3));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 5, 4));        
		
        $this->addFrontSystem(new RapidGatling(2, 4, 1, 240, 120));
        $this->addFrontSystem(new GaussCannon(3, 10, 4, 300, 60));
        $this->addFrontSystem(new GaussCannon(3, 10, 4, 300, 60));
        $this->addFrontSystem(new GaussCannon(3, 10, 4, 300, 60));
        $this->addFrontSystem(new Thruster(1, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(1, 6, 0, 2, 1));

        $this->addAftSystem(new RapidGatling(1, 4, 1, 120, 360));
        $this->addAftSystem(new RapidGatling(1, 4, 1, 0, 240));   
        $this->addAftSystem(new Thruster(2, 6, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 8, 0, 4, 2));
        $this->addAftSystem(new Thruster(2, 6, 0, 3, 2));
        
       
        $this->addPrimarySystem(new Structure(4, 46));
		


	//d20 hit chart
	$this->hitChart = array(
		
		0=> array( //PRIMARY
			8 => "Thruster",
			11 => "Scanner",
			15 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),
		1=> array( //Fwd
			5 => "Thruster",
			10 => "Gauss Cannon",
			11 => "Rapid Gatling Railgun",
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
