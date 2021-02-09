<?php
class SshelathUshula extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 200;
        $this->faction = "ZEscalation Sshel'ath Alliance";
        $this->phpclass = "SshelathUshula";
        $this->imagePath = "img/ships/EscalationWars/SshelathUshula.png";
        $this->shipClass = "Ushula Assault Frigate";
		$this->unofficial = true;
        $this->canvasSize = 60;
	    $this->isd = 1962;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 11;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 60;
        
        $this->addPrimarySystem(new Reactor(6, 7, 0, 0));
        $this->addPrimarySystem(new CnC(6, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 9, 2, 4));
        $this->addPrimarySystem(new Engine(6, 9, 0, 6, 2));
        $this->addPrimarySystem(new Hangar(4, 1));
        $this->addPrimarySystem(new Thruster(4, 10, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(4, 10, 0, 3, 4));        
        

		$this->addFrontSystem(new EWLightGaussCannon(2, 6, 3, 180, 60));
		$this->addFrontSystem(new EWLightGaussCannon(2, 6, 3, 300, 180));
		$this->addFrontSystem(new CustomIndustrialGrappler(3, 5, 0, 300, 60));
		$this->addFrontSystem(new CustomIndustrialGrappler(3, 5, 0, 300, 60));
        $this->addFrontSystem(new Thruster(4, 13, 0, 4, 1));
	    
        $this->addAftSystem(new Thruster(4, 8, 0, 3, 2));    
        $this->addAftSystem(new Thruster(4, 8, 0, 3, 2));    
       
        $this->addPrimarySystem(new Structure(6, 30));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			10 => "Thruster",
			12 => "Scanner",
			14 => "Engine",
			16 => "Hangar",
			18 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			5 => "Thruster",
			7 => "Industrial Grappler",
			10 => "Light Gauss Cannon",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			7 => "Thruster",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
