<?php
class CentauriCivilianFreighter extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 125;
		$this->faction = "Civilians";
        $this->phpclass = "CentauriCivilianFreighter";
        $this->imagePath = "img/ships/CentauriCivilianFreighter.png";
        $this->shipClass = "Centauri Civilian Freighter";
        $this->canvasSize = 85;

	    $this->isCombatUnit = false; //not a combat unit, it will never be present in a regular battlegroup
		$this->fighters = array("cargo shuttles"=>4);     

		$this->isd = 2175;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 999;
        $this->pivotcost = 999;
        
         
        $this->addPrimarySystem(new Reactor(4, 4, 0, 0));
        $this->addPrimarySystem(new CnC(3, 5, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 2, 4));
        $this->addPrimarySystem(new Engine(4, 6, 0, 4, 3));
		$this->addPrimarySystem(new Hangar(2, 4, 1));
		$this->addPrimarySystem(new TwinArray(2, 6, 2, 0, 360));
		$this->addPrimarySystem(new Thruster(3, 10, 0, 2, 3));
		$this->addPrimarySystem(new Thruster(3, 10, 0, 2, 4));
		        		
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new CargoBay(2, 20));
        $this->addFrontSystem(new CargoBay(2, 20));

		
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new CargoBay(2, 20));
        $this->addAftSystem(new CargoBay(2, 20));
        $this->addAftSystem(new CargoBay(2, 20));
        $this->addAftSystem(new CargoBay(2, 20));
     
               
        $this->addPrimarySystem(new Structure(4, 52));
		
        $this->hitChart = array (
        		0=> array (
        				7=>"Thruster",
        				9=>"Twin Array",
        				12=>"Scanner",
        				15=>"Engine",
        				17=>"Hangar",
        				19=>"Reactor",
        				20=>"C&C",
        		),
        		1=> array (
        				5=>"Thruster",
        				8=>"Cargo Bay A",
        				11=>"Cargo Bay B",
        				17=>"Structure",
        				20=>"Primary",
        		),
        		2=> array(
        				3=>"Thruster",
        				5=>"Cargo Bay C",
        				7=>"Cargo Bay D",
        				9=>"Cargo Bay E",
        				11=>"Cargo BayF",
        				17=>"Structure",
        				20=>"Primary",
        		),
        );
    }

}



?>
