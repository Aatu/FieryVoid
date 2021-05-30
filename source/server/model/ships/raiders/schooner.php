<?php
class Schooner extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 200;
	$this->faction = "Raiders";
        $this->phpclass = "Schooner";
        $this->imagePath = "img/ships/civilianTanker.png";
        $this->shipClass = "Schooner";
        $this->canvasSize = 100;
        
		$this->notes = "Generic raider unit.";
		$this->notes .= "<br> ";

		$this->isd = 2183;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 999;
        $this->pivotcost = 999;
        $this->iniativebonus = 20;
        $this->fighters = array("light"=>12);        
         
        $this->addPrimarySystem(new Reactor(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 8, 3, 4));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 3, 3));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 3, 4));
		
		$temp1 = new CargoBay(3, 16);
		$temp2 = new CargoBay(3, 16);
		$temp3 = new CargoBay(3, 16);
		$temp4 = new CargoBay(3, 16);
		$temp5 = new CargoBay(3, 16);
		$temp6 = new CargoBay(3, 16);
		$temp1->displayName = "cargoBayA";
		$temp2->displayName = "cargoBayB";
		$temp3->displayName = "cargoBayC";
		$temp4->displayName = "cargoBayD";
		$temp5->displayName = "cargoBayE";
		$temp6->displayName = "cargoBayF";
		$this->addFrontSystem($temp1);
		$this->addFrontSystem($temp2);
		$this->addFrontSystem($temp3);
		$this->addAftSystem($temp4);
		$this->addAftSystem($temp5);
		$this->addAftSystem($temp6);
		
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
		$this->addFrontSystem(new CnC(4, 9, 0, 0));
		$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 240, 120));
		$this->addFrontSystem(new LightLaser(2, 4, 3, 180, 0));
		$this->addFrontSystem(new LightLaser(2, 4, 3, 0, 180));

		$this->addAftSystem(new Hangar(3, 4));
		$this->addAftSystem(new Engine(3, 9, 0, 6, 4));
		$this->addAftSystem(new StdParticleBeam(2, 4, 1, 60, 300));
		$this->addAftSystem(new Thruster(2, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 8, 0, 3, 2));
	
        $this->addPrimarySystem(new Structure( 4, 64));
        
        $this->hitChart = array(
        		0=> array(
        				14 => "Thruster",
        				17 => "Scanner",
        				20 => "Reactor",
        		),
        		1=> array(
        				4 => "Thruster",
        				6 => "cargoBayA",
        				8 => "cargoBayB",
        				10 => "cargoBayC",
        				11 => "Standard Particle Beam",
        				12 => "C&C",
        				14 => "Light Laser",
        				17 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				4 => "Thruster",
        				6 => "cargoBayD",
        				8 => "cargoBayE",
        				10 => "cargoBayF",
        				11 => "Standard Particle Beam",
        				12 => "Engine",
        				13 => "Hangar",
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
?>
