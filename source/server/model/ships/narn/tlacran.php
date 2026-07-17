<?php
class Tlacran extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 130;
		$this->faction = "Civilians";
        $this->phpclass = "Tlacran";
        $this->imagePath = "img/ships/NarnTlacran.png";
        $this->shipClass = "Narn T'lacran Barge (2243 Refit)";
		$this->occurence = "common";
		$this->variantOf = "Narn T'lacran Barge";   
        $this->canvasSize = 100;
		$this->fighters = array("cargo shuttles"=>1);         
	    $this->isCombatUnit = false; //not a combat unit, it will never be present in a regular battlegroup



		$this->isd = 2243;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
    	$this->iniativebonus = -4 * 5;
    	$this->fighters = array("medium"=>12);
         
        $this->addPrimarySystem(new Reactor(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 2, 2));
        $this->addPrimarySystem(new Engine(3, 6, 0, 4, 3));        
    	$this->addPrimarySystem(new Thruster(2, 9, 0, 2, 3));
    	$this->addPrimarySystem(new Thruster(2, 9, 0, 2, 4));
		
        $this->addFrontSystem(new CnC(3, 4, 0, 0));
        $this->addFrontSystem(new Hangar(3, 1));
        $this->addFrontSystem(new Thruster(2, 9, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 9, 0, 2, 1));
        $this->addFrontSystem(new LightPulse(2, 4, 1, 240, 360));
        $this->addFrontSystem(new LightPulse(3, 6, 3, 0, 120));		

        $this->addAftSystem(new CargoBay(0, 160));
        $this->addAftSystem(new Thruster(2, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 9, 0, 2, 2));
	
        $this->addPrimarySystem(new Structure( 4, 46));
        
        $this->hitChart = array(
        		0=> array(
        				11 => "Thruster",
        				14 => "Scanner",
        				17 => "Engine",
        				20 => "Reactor",

        		),
        		1=> array(
        				4 => "Thruster",
        				6 => "Medium Plasma Cannon",
        				8 => "Light Particle Beam",
        	        	9 => "C&C",	
        	        	10 => "Hangar",		
        				17 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				10 => "Hangar",	
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
    }

}



?>
