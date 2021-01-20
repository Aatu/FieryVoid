<?php
class SshelathAraunax extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 325;
        $this->faction = "ZEscalation Sshelath";
        $this->phpclass = "SshelathAraunax";
        $this->imagePath = "img/ships/EscalationWars/SshelathAraunax.png";
        $this->shipClass = "Araunax Attack Frigate";
		$this->unofficial = true;
        $this->canvasSize = 60;
	    $this->isd = 1956;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 60;
        
        $this->addPrimarySystem(new Reactor(4, 15, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 3, 6));
        $this->addPrimarySystem(new Engine(4, 11, 0, 10, 2));
        $this->addPrimarySystem(new Hangar(4, 1));
        $this->addPrimarySystem(new Thruster(2, 13, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(2, 13, 0, 4, 4));        
        

		$this->addFrontSystem(new EWLightGaussCannon(2, 6, 3, 270, 90));
		$this->addFrontSystem(new LightLaser(3, 4, 3, 240, 360));
		$this->addFrontSystem(new LightLaser(3, 4, 3, 240, 360));
		$this->addFrontSystem(new LightLaser(3, 4, 3, 0, 120));
		$this->addFrontSystem(new LightLaser(3, 4, 3, 0, 120));
        $this->addFrontSystem(new Thruster(3, 8, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 4, 1));
	    
		$this->addAftSystem(new EWLightGaussCannon(2, 6, 3, 90, 270));
        $this->addAftSystem(new Thruster(3, 14, 0, 5, 2));    
        $this->addAftSystem(new Thruster(3, 14, 0, 5, 2));    
       
        $this->addPrimarySystem(new Structure(4, 60));

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
			3 => "Thruster",
			7 => "Light Laser",
			9 => "Light Gauss Cannon",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Thruster",
			8 => "Light Gauss Cannon",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
