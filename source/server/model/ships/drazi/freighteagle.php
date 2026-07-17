<?php
class Freighteagle extends MediumShipLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 140;
		$this->faction = "Civilians";
        $this->phpclass = "Freighteagle";
        $this->imagePath = "img/ships/drazi/DraziFreighteagle.png";
        $this->shipClass = "Drazi Freighteagle";
        $this->agile = true;
        $this->canvasSize = 100;
		$this->isd = 2143;
	    $this->isCombatUnit = false; //not a combat unit, it will never be present in a regular battlegroup

        $this->forwardDefense = 12;
        $this->sideDefense = 11;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.5;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 1;
		$this->iniativebonus = 70;

        $this->addFrontSystem(new StdParticleBeam(4, 4, 1, 240, 120));
        $this->addPrimarySystem(new Reactor(4, 5, 0, 0));
        $this->addPrimarySystem(new CnC(4, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 8, 4, 4));
        $this->addPrimarySystem(new Engine(4, 10, 0, 5, 3));
		$this->addPrimarySystem(new Hangar(2, 1, 1));
		$this->addAftSystem(new Thruster(2, 8, 0, 4, 1));
		$this->addAftSystem(new Thruster(3, 11, 0, 5, 2));
		
        $this->addLeftSystem(new Thruster(3, 8, 0, 2, 3));
		$this->addLeftSystem(new CargoBay(3, 12));

		
        $this->addRightSystem(new Thruster(3, 8, 0, 2, 4));
		$this->addRightSystem(new CargoBay(3, 12));

		
        $this->addPrimarySystem(new Structure( 3, 32));
    
            $this->hitChart = array(
        		0=> array(
        				8=> "Structure",
					10 => "2:Thruster",
        				12 => "1:Standard Particle Beam",
					14 => "Scanner",
        				16 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		3=> array(
        				5 => "Thruster",
        				9 => "Cargo Bay",
        				20 => "Primary",
        		),
        		4=> array(
        				5 => "Thruster",
        				9 => "Cargo Bay",
        				20 => "Primary",
        		),
        );
    
    }

}

?>
