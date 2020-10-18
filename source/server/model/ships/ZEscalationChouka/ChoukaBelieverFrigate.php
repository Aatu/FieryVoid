<?php
class ChoukaBelieverFrigate extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 225;
        $this->faction = "ZEscalation Chouka";
        $this->phpclass = "ChoukaBelieverFrigate";
        $this->imagePath = "img/ships/EscalationWars/ChoukaBelieverFrigate.png";
        $this->shipClass = "Believer Frigate";
		$this->unofficial = true;
        $this->agile = true;
        $this->canvasSize = 75;
	    $this->isd = 1880;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 10;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 60;
        
         
        $this->addPrimarySystem(new Reactor(2, 9, 0, 0));
        $this->addPrimarySystem(new CnC(3, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(2, 9, 4, 4));
        $this->addPrimarySystem(new Engine(2, 9, 0, 10, 2));
        $this->addPrimarySystem(new Hangar(2, 1));
        $this->addPrimarySystem(new Thruster(1, 8, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(1, 8, 0, 5, 4));        
        
        $this->addFrontSystem(new EWHeavyPlasmaGun(2, 5, 3, 240, 60));
        $this->addFrontSystem(new EWHeavyPlasmaGun(2, 5, 3, 300, 120));
		$this->addFrontSystem(new EWPointPlasmaGun(1, 3, 1, 270, 90));
		$this->addFrontSystem(new EWPointPlasmaGun(1, 3, 1, 270, 90));
        $this->addFrontSystem(new Thruster(1, 5, 0, 3, 1));
        $this->addFrontSystem(new Thruster(1, 5, 0, 3, 1));
	    
		$this->addAftSystem(new EWPointPlasmaGun(1, 3, 1, 90, 270));
        $this->addAftSystem(new Thruster(1, 7, 0, 5, 2));    
        $this->addAftSystem(new Thruster(1, 7, 0, 5, 2));    
       
        $this->addPrimarySystem(new Structure(2, 32));


	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			5 => "Thruster",
			10 => "Scanner",
			13 => "Engine",
			15 => "Hangar",
			18 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			5 => "Thruster",
			7 => "Heavy Plasma Gun",
			9 => "Point Plasma Gun",
			16 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Thruster",
			8 => "Point Plasma Gun",
			16 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
