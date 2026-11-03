<?php
class MakarFreighter extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 120;
		$this->faction = "Nexus Support Units";
        $this->phpclass = "MakarFreighter";
        $this->imagePath = "img/ships/civilianFreighter.png";
        $this->shipClass = "Makar Selkan Freighter";
        $this->canvasSize = 100;
	    $this->isCombatUnit = false; //not a combat unit, it will never be present in a regular battlegroup
		$this->unofficial = true;
		$this->isd = 1890;
		$this->fighters = array("cargo shuttles"=>4); 

        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 4;
        $this->rollcost = 999;
        $this->pivotcost = 999;
		$this->iniativebonus = -20;
		
		$cA = new CargoBay(2, 30);
		$cB = new CargoBay(2, 30);
		$cC = new CargoBay(2, 30);
		$cD = new CargoBay(2, 30);
		$cE = new CargoBay(2, 30);
		$cF = new CargoBay(2, 30);
		$cG = new CargoBay(2, 30);
		$cH = new CargoBay(2, 30);
		
		$cA->displayName = "Cargo Bay A";
		$cB->displayName = "Cargo Bay B";
		$cC->displayName = "Cargo Bay C";
		$cD->displayName = "Cargo Bay D";
		$cE->displayName = "Cargo Bay E";
		$cF->displayName = "Cargo Bay F";
		$cG->displayName = "Cargo Bay G";
		$cH->displayName = "Cargo Bay H";
         
        $this->addPrimarySystem(new Reactor(3, 4, 0, 0));
        $this->addPrimarySystem(new CnC(4, 4, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 2, 2));
        $this->addPrimarySystem(new Engine(3, 6, 0, 6, 4));
		$this->addPrimarySystem(new Hangar(1, 4, 2));
		$this->addPrimarySystem(new NexusDefenseGun(2, 4, 1, 0, 360));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 6, 3));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 6, 4));
		
        $this->addFrontSystem(new Thruster(2, 15, 0, 6, 1));
        $this->addFrontSystem($cA);
        $this->addFrontSystem($cB);
        $this->addFrontSystem($cC);
        $this->addFrontSystem($cD);
		
        $this->addAftSystem(new Thruster(2, 15, 0, 6, 2));
        $this->addAftSystem($cE);
        $this->addAftSystem($cF);
        $this->addAftSystem($cG);
        $this->addAftSystem($cH);
       
        $this->addPrimarySystem(new Structure( 3, 48));
        
        $this->hitChart = array(
        		0=> array(
        				7 => "Thruster",
        				9 => "Defense Gun",
        				12 => "Scanner",
        				15 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				3 => "Thruster",
        				5 => "Cargo Bay A",
        				7 => "Cargo Bay B",
        				9 => "Cargo Bay C",
        				11 => "Cargo Bay D",
        				17 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				3 => "Thruster",
        				5 => "Cargo Bay E",
        				7 => "Cargo Bay F",
        				9 => "Cargo Bay G",
        				11 => "Cargo Bay H",
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
?>
