<?php
class CentauriJavelinFastCourier extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 275;
		$this->faction = "Civilians";
        $this->phpclass = "CentauriJavelinFastCourier";
        $this->imagePath = "img/ships/CentauriJavelin.png";
        $this->shipClass = "Centauri Javelin Fast Courier";
        $this->canvasSize = 85;

	    $this->isCombatUnit = false; //not a combat unit, it will never be present in a regular battlegroup
		$this->fighters = array("cargo shuttles"=>2);     

		$this->isd = 2201;
		
		
		$this->notes = 'Atmospheric Capable.';
        $this->agile = true;

        $this->forwardDefense = 12;
        $this->sideDefense = 13;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 12 *5;

         
        $this->addPrimarySystem(new Reactor(5, 10, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 10, 3, 7));
        $this->addPrimarySystem(new Engine(4, 11, 0, 10, 2));
		$this->addPrimarySystem(new Hangar(4, 2, 1));
        $this->addFrontSystem(new CargoBay(4, 8));
		$this->addPrimarySystem(new Thruster(3, 10, 0, 4, 3));
		$this->addPrimarySystem(new Thruster(3, 10, 0, 4, 4));
        $this->addPrimarySystem(new Quarters(2, 6));
		        		
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new CargoBay(2, 20));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 240, 120));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 240, 120));

		
        $this->addAftSystem(new Thruster(3, 12, 0, 5, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 5, 2));
		$this->addAftSystem(new TwinArray(3, 6, 2, 60, 300));  


     
               
        $this->addPrimarySystem(new Structure(5, 45));
		
        $this->hitChart = array (
        		0=> array (
        				6=>"Thruster",
        				9=>"Cargo Bay",
        				11=>"Quarters",
        				12=>"Scanner",
        				15=>"Engine",
        				17=>"Hangar",
        				19=>"Reactor",
        				20=>"C&C",
        		),
        		1=> array (
        				6=>"Thruster",
        				11=>"Twin Array",
        				17=>"Structure",
        				20=>"Primary",
        		),
        		2=> array(
        				5=>"Thruster",
        				8=>"Twin Array",
        				17=>"Structure",
        				20=>"Primary",
        		),
        );
    }

}



?>
