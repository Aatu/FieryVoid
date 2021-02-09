<?php
class SshelathVahskal extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 150;
        $this->faction = "ZEscalation Sshel'ath Alliance";
        $this->phpclass = "SshelathVahskal";
        $this->imagePath = "img/ships/EscalationWars/SshelathVahskal.png";
        $this->shipClass = "Vahskal Patrol Carrier";
		$this->unofficial = true;
        $this->canvasSize = 85;
	    $this->isd = 1932;
		
        $this->fighters = array("normal"=>12);
        
        $this->forwardDefense = 12;
        $this->sideDefense = 14;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 60;
        
        $this->addPrimarySystem(new Reactor(3, 7, 0, 0));
        $this->addPrimarySystem(new CnC(4, 5, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 7, 2, 3));
        $this->addPrimarySystem(new Engine(3, 7, 0, 4, 1));
        $this->addPrimarySystem(new Hangar(2, 2));
        $this->addPrimarySystem(new Thruster(2, 8, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(2, 8, 0, 3, 4));   
        
		$this->addFrontSystem(new EWDefenseLaser(1, 2, 1, 240, 60));
		$this->addFrontSystem(new EWDefenseLaser(1, 2, 1, 300, 120));
		$this->addFrontSystem(new LightLaser(1, 4, 3, 270, 90));
        $this->addFrontSystem(new Thruster(2, 6, 0, 1, 1));
        $this->addFrontSystem(new Thruster(2, 6, 0, 1, 1));
	    
		$this->addAftSystem(new EWDefenseLaser(1, 2, 1, 120, 300));
		$this->addAftSystem(new EWDefenseLaser(1, 2, 1, 60, 240));
        $this->addAftSystem(new Thruster(2, 12, 0, 4, 2));    
       
        $this->addPrimarySystem(new Structure(3, 52));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			8 => "Thruster",
			11 => "Scanner",
			14 => "Engine",
			16 => "Hangar",
			18 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			5 => "Thruster",
			6 => "Light Laser",
			9 => "Defense Laser",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Thruster",
			10 => "Defense Laser",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
