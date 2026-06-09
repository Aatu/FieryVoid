<?php
class SshelathSkaggha extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 415;
        $this->faction = "Escalation Wars Sshel'ath Alliance";
        $this->phpclass = "SshelathSkaggha";
        $this->imagePath = "img/ships/EscalationWars/SshelathSkavna.png";
        $this->shipClass = "Skaggha Escort Frigate";
			$this->variantOf = "Skavna Torpedo Frigate";
			$this->occurence = "common";
		$this->unofficial = true;
        $this->canvasSize = 90;
	    $this->isd = 1985;

	    $this->notes = 'Atmospheric capable';
        
        $this->forwardDefense = 13;
        $this->sideDefense = 11;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 1;
        $this->iniativebonus = 60;
        
        $this->addPrimarySystem(new Reactor(4, 10, 0, 0));
        $this->addPrimarySystem(new CnC(6, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 3, 6));
        $this->addPrimarySystem(new Hangar(4, 1, 1));
        $this->addPrimarySystem(new Engine(4, 11, 0, 10, 2));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 4, 4));        

		$this->addFrontSystem(new EWGatlingLaser(2, 7, 4, 240, 60));
        $this->addFrontSystem(new EWDefenseLaser2(1, 2, 1, 240, 120));
        $this->addFrontSystem(new EWDefenseLaser2(1, 2, 1, 240, 120));
		$this->addFrontSystem(new EWGatlingLaser(2, 7, 4, 300, 60));
        $this->addFrontSystem(new EWDefenseLaser2(1, 2, 1, 240, 120));
        $this->addFrontSystem(new EWDefenseLaser2(1, 2, 1, 240, 120));
		$this->addFrontSystem(new EWGatlingLaser(2, 7, 4, 300, 120));
        $this->addFrontSystem(new Thruster(2, 8, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 8, 0, 2, 1));
	    
        $this->addAftSystem(new EWDefenseLaser2(1, 2, 1, 60, 300));
        $this->addAftSystem(new EWDefenseLaser2(1, 2, 1, 60, 300));
        $this->addAftSystem(new Thruster(3, 15, 0, 10, 2));    
       
        $this->addPrimarySystem(new Structure(4, 42));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			8 => "Thruster",
			11 => "Scanner",
			14 => "Engine",
			16 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			4 => "Thruster",
			7 => "Gatling Laser",
			10 => "Defense Laser II",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			7 => "Thruster",
			10 => "Defense Laser II",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
