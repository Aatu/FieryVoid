<?php
class Caravel extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 310;
		$this->faction = "Raiders";
        $this->phpclass = "Caravel";
        $this->imagePath = "img/ships/RaiderCaravel.png";
        $this->shipClass = "Caravel";
        $this->canvasSize = 100;

		$this->notes = "Generic raider unit.";
		$this->notes .= "<br> ";
		
		$this->fighters = array("cargo shuttles"=>2); 	

		$this->isd = 2221;
        $this->unofficial = true;
		
        $this->forwardDefense = 11;
        $this->sideDefense = 11;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 1;
    	$this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(3, 15, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 4, 5));
        $this->addPrimarySystem(new CargoBay(3, 18));
        $this->addPrimarySystem(new Hangar(3, 2, 1));
    	$this->addPrimarySystem(new Thruster(3, 10, 0, 3, 3));
    	$this->addPrimarySystem(new Thruster(3, 10, 0, 3, 4));
		
        $this->addFrontSystem(new Thruster(3, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 6, 0, 2, 1));
        $this->addFrontSystem(new HeavyPlasma(3, 8, 5, 300, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 1, 1, 240, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 1, 1, 300, 120));

        $this->addAftSystem(new Engine(3, 11, 0, 6, 2));
        $this->addAftSystem(new LightPlasma(2, 4, 2, 180, 360));
        $this->addAftSystem(new LightPlasma(2, 4, 2, 0, 180));
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
	
        $this->addPrimarySystem(new Structure( 4, 40));
        
        $this->hitChart = array(
        		0=> array(
        				10 => "Thruster",
        				13 => "Cargo Bay",
        				16 => "Scanner",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				6 => "Thruster",
        				8 => "Heavy Plasma",
        				10 => "Light Particle Beam",
        				17 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				8 => "Light Plasma Cannon",
        				9 => "Engine",
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
    }

}



?>
