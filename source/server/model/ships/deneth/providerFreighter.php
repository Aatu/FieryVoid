<?php
class ProviderFreighter extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 110;
	$this->faction = "Civilians";
        $this->phpclass = "providerfreighter";
        $this->imagePath = "img/ships/DenethProvider.png";
        $this->shipClass = "Deneth Provider Freighter";
        $this->canvasSize = 100;
	    
	$this->isd = 2210;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
	$this->iniativebonus = -20;
		
        $this->addPrimarySystem(new Reactor(4, 6, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 8, 2, 3));
        $this->addPrimarySystem(new Engine(4, 10, 0, 6, 3));
	$this->addPrimarySystem(new Hangar(4, 2));
	$this->addPrimarySystem(new TwinArray(2, 6, 2, 0, 360));
	$this->addPrimarySystem(new Thruster(3, 10, 0, 4, 3));
	$this->addPrimarySystem(new Thruster(3, 10, 0, 4, 4));
		
        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
	$this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
	$this->addFrontSystem(new CargoBay(4, 30));
        $this->addFrontSystem(new CargoBay(4, 30));
		
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
	$this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new CargoBay(4, 30));
        $this->addAftSystem(new CargoBay(4, 30));
       
        $this->addPrimarySystem(new Structure( 3, 30));
        
        $this->hitChart = array(
        		0=> array(
        				8 => "Thruster",
        				10 => "Twin Array",
        				13 => "Scanner",
        				16 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				5 => "Thruster",
        				10 => "Cargo Bay",
        				17 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				5 => "Thruster",
        				10 => "Cargo Bay",
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
?>
