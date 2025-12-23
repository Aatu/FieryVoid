<?php
class SshelathStoaka extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 175;
        $this->faction = "Escalation Wars Sshel'ath Alliance";
        $this->phpclass = "SshelathStoaka";
        $this->imagePath = "img/ships/EscalationWars/SshelathStoaka.png";
        $this->shipClass = "Stoaka Destroyer";
		$this->unofficial = true;
        $this->canvasSize = 90;
	    $this->isd = 1914;

	    $this->notes = 'A-hel-is Faction';
	    $this->notes .= '<br>Light missiles only';
        
        $this->forwardDefense = 12;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 60;
        
        $this->addPrimarySystem(new Reactor(3, 10, 0, 0));
        $this->addPrimarySystem(new CnC(3, 11, 0, 0));
        $this->addPrimarySystem(new AntiquatedScanner(3, 7, 2, 3));
        $this->addPrimarySystem(new Hangar(3, 2));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 2, 3));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 2, 4));        

		$this->addFrontSystem(new CustomLightOMissileRack(2, 6, 0, 240, 60));
		$this->addFrontSystem(new CustomLightOMissileRack(2, 6, 0, 270, 90));
		$this->addFrontSystem(new CustomLightOMissileRack(2, 6, 0, 300, 120));
        $this->addFrontSystem(new Thruster(2, 8, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 8, 0, 2, 1));
	    
		$this->addAftSystem(new EWDefenseLaser(1, 2, 1, 0, 360));
		$this->addAftSystem(new EWDefenseLaser(1, 2, 1, 0, 360));
        $this->addAftSystem(new Engine(2, 11, 0, 6, 2));
        $this->addAftSystem(new Thruster(2, 16, 0, 6, 2));    
       
        $this->addPrimarySystem(new Structure(2, 60));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			9 => "Thruster",
			12 => "Scanner",
			15 => "Hangar",
			18 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			5 => "Thruster",
			8 => "Light Class-O Missile Rack",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			5 => "Thruster",
			7 => "Defense Laser",
			10 => "Engine",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
