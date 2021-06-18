<?php
class Pinnace extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 240;
		$this->faction = "Raiders";
        $this->phpclass = "Pinnace";
//        $this->imagePath = "img/ships/pinnace.png";
        $this->imagePath = "img/ships/raider_pinnace.png";
        $this->shipClass = "Pinnace";
        $this->canvasSize = 85;

		$this->notes = "Generic raider unit.";
		$this->notes .= "<br> ";
		$this->notes .= "<br>More detailed deployment restrictions are in the Faction List document.";
		$this->notes .= "<br> ";

		$this->isd = 2182;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        
         
        $this->addPrimarySystem(new Reactor(4, 9, 0, 0));
        $this->addPrimarySystem(new CnC(3, 5, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 2, 4));
        $this->addPrimarySystem(new Engine(4, 6, 0, 4, 2));
		$this->addPrimarySystem(new Hangar(2, 4));
		$this->addPrimarySystem(new TwinArray(2, 6, 2, 0, 360));
		$this->addPrimarySystem(new Thruster(3, 10, 0, 2, 3));
		$this->addPrimarySystem(new Thruster(3, 10, 0, 2, 4));
		        		
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 360));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 0, 60));
		
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new CargoBay(2, 16));
		$this->addAftSystem(new TwinArray(2, 6, 2, 180, 360));
        $this->addAftSystem(new TwinArray(2, 6, 2, 0, 180));        
               
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
        				9=>"Medium Plasma Cannon",
        				17=>"Structure",
        				20=>"Primary",
        		),
        		2=> array(
        				6=>"Thruster",
        				9=>"Twin Array",
        				11=>"Cargo Bay",
        				17=>"Structure",
        				20=>"Primary",
        		),
        );
    }

}



?>
