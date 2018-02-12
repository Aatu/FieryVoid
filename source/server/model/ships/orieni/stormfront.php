<?php
class StormFront extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 390;
		$this->faction = "Orieni";
        $this->phpclass = "StormFront";
        $this->imagePath = "img/ships/stormFront.png";
        $this->shipClass = "Storm Front Missile Corvette";
		$this->variantOf = "Steadfast Escort Corvette";
		$this->isd = 2007;	    
		$this->occurence = "uncommon";
	    
        $this->agile = true;
        $this->canvasSize = 100;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
		$this->iniativebonus = 60;
        
         
        $this->addPrimarySystem(new Reactor(4, 15, 0, 2));
        $this->addPrimarySystem(new CnC(4, 15, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 3, 5));
        $this->addPrimarySystem(new Engine(3, 15, 0, 10, 2));
		$this->addPrimarySystem(new Hangar(1, 1));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 5, 3));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 5, 4));        
		
        $this->addFrontSystem(new SMissileRack(3, 6, 0, 240, 60));
        $this->addFrontSystem(new SMissileRack(3, 6, 0, 270, 90));
        $this->addFrontSystem(new SMissileRack(3, 6, 0, 300, 120));
        $this->addFrontSystem(new Thruster(1, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(1, 6, 0, 2, 1));

        $this->addAftSystem(new RapidGatling(1, 4, 1, 120, 360));
        $this->addAftSystem(new RapidGatling(1, 4, 1, 0, 240)); 
        $this->addAftSystem(new Thruster(1, 6, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 8, 0, 4, 2));
        $this->addAftSystem(new Thruster(1, 6, 0, 3, 2));
        
       
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
			6 => "Thruster",
			8 => "Class-S Missile Rack",
			17 => "Structure",
			20 => "Primary",
		),
		2=> array( //Aft
			7 => "Thruster",
			9 => "Rapid Gatling Railgun",
			17 => "Structure",
			20 => "Primary",
		),
	); //end of hit chart
		
        }
    }
?>
