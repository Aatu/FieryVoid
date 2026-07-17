<?php
class Transbird extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	    $this->pointCost = 375;
	    $this->faction = "Civilians";
        $this->phpclass = "Transbird";
        $this->imagePath = "img/ships/drazi/DraziTransbird.png";
        $this->shipClass = "Transbird Freighter";
        $this->fighters = array("cargoshuttle" => 1);
	    $this->isd = 2135;
        $this->canvasSize = 200;
	    $this->isCombatUnit = false; //not a combat unit, it will never be present in a regular battlegroup

        $this->forwardDefense = 13;
        $this->sideDefense = 12;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 40;


        $this->addPrimarySystem(new Reactor(5, 12, 0, 6));
        $this->addPrimarySystem(new CnC(5, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 4, 7));
        $this->addPrimarySystem(new Engine(5, 11, 0, 8, 2));
        $this->addPrimarySystem(new Hangar(4, 1));
		
        $this->addFrontSystem(new CargoBay(3, 16));
        $this->addFrontSystem(new Thruster(4, 13, 0, 4, 1));
		
        $this->addAftSystem(new Thruster(5, 19, 0, 8, 2));
		
        $this->addLeftSystem(new CargoBay(3, 32));
        $this->addLeftSystem(new StdParticleBeam(3, 4, 1, 240, 60));
        $this->addLeftSystem(new StdParticleBeam(3, 4, 1, 240, 60));
        $this->addLeftSystem(new Thruster(4, 13, 0, 4, 3));
		
        $this->addRightSystem(new CargoBay(3, 32));
        $this->addRightSystem(new StdParticleBeam(3, 4, 1, 300, 120));
        $this->addRightSystem(new StdParticleBeam(3, 4, 1, 300, 120));
        $this->addRightSystem(new Thruster(4, 13, 0, 4, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(5, 32));
        $this->addLeftSystem(new Structure(4, 40));
        $this->addRightSystem(new Structure(4, 40));
    
		$this->hitChart = array(
			0=> array(
				8 => "Structure",
				11 => "2:Thruster",
				12 => "Cargo Bay",
				14 => "Scanner",
				16 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			3=> array(
				3 => "Thruster",
				5 => "Cargo Bay",
				9 => "Standard Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				3 => "Thruster",
				5 => "Cargo Bay",
				9 => "Standard Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
        );
    
    }
}
?>
