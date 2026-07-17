<?php
class Allanti extends BaseShip{
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 425;
        $this->faction = "Civilians";
        $this->phpclass = "Allanti";
        $this->imagePath = "img/ships/AbbaiAllanti.png";
        $this->shipClass = "Abbai Allanti Freighter";
		$this->canvasSize = 200; 
        $this->shipSizeClass = 3;
	    $this->isCombatUnit = false; //not a combat unit, it will never be present in a regular battlegroup
		$this->fighters = array("cargo shuttles"=>4);         

	    $this->isd = 2235;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 2;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
         
        $this->addPrimarySystem(new Reactor(5, 15, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 18, 6, 8));
        $this->addPrimarySystem(new Engine(5, 16, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(5, 4, 1));
        $this->addPrimarySystem(new ShieldGenerator(5, 16, 4, 3));
		
		
        $this->addFrontSystem(new Thruster(4, 20, 0, 6, 1));
        $this->addFrontSystem(new QuadArray(3, 0, 0, 240, 60));
        $this->addFrontSystem(new QuadArray(3, 0, 0, 300, 120));
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 2, 300, 360));
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 2, 0, 60));
		
		
        $this->addRightSystem(new Thruster(3, 13, 0, 5, 4));
        $this->addRightSystem(new CargoBay(4, 40));
        $this->addRightSystem(new CargoBay(4, 40));
        $this->addRightSystem(new Particleimpeder(2, 0, 0, 0, 180));
        $this->addRightSystem(new GraviticShield(0, 6, 0, 2, 60, 120));

        
        $this->addLeftSystem(new Thruster(3, 13, 0, 5, 3));
        $this->addLeftSystem(new CargoBay(4, 40));
		$this->addLeftSystem(new CargoBay(4, 40));
        $this->addLeftSystem(new Particleimpeder(2, 0, 0, 180, 360));
        $this->addLeftSystem(new GraviticShield(0, 6, 0, 2, 240, 300));

        $this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
        $this->addAftSystem(new QuadArray(3, 0, 0, 90, 270));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 3, 180, 240));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 3, 120, 180));
		
		
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 32));
        $this->addAftSystem(new Structure( 5, 32));
        $this->addLeftSystem(new Structure( 4, 40));
        $this->addRightSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 5, 32));       
        
        $this->hitChart = array(
        		0=> array(
        				7 => "Structure",
						9 => "Shield Generator",
        				12 => "Scanner",
        				15 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				5 => "Thruster",
        				7 => "Gravitic Shield",
						10 => "Quad Array",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				4 => "Thruster",
        				5 => "Gravitic Shield",
						7 => "Particle Impeder",
						12=> "Particle Impeder",
        				17 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				6 => "Thruster",
        				8 => "Gravitic Shield",
						10 => "Quad Array",
        				17 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				4 => "Thruster",
        				5 => "Gravitic Shield",
						7 => "Particle Impeder",
						12=> "Particle Impeder",
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
