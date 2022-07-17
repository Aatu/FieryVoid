<?php
class Shobogna extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 260;
		$this->faction = "Pakmara";
        $this->phpclass = "Shobogna";
        $this->imagePath = "img/ships/PakmaraShobogna.png";
        $this->shipClass = "Sho'Bog'Na Patroller";
        $this->canvasSize = 100;

        $this->isd = 2188;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 1;
		$this->iniativebonus = 11*5;
		
        $this->addPrimarySystem(new Reactor(3, 10, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 6, 4, 5));
		$this->addPrimarySystem(new CnC(3, 6, 0, 0));
        $this->addPrimarySystem(new Engine(3, 9, 0, 9, 2));
        $this->addPrimarySystem(new Hangar(2, 2));
		$this->addPrimarySystem(new Thruster(3, 10, 0, 4, 3));
		$this->addPrimarySystem(new Thruster(3, 10, 0, 4, 4));
		$this->addPrimarySystem(new PakmaraPlasmaWeb(2, 4, 2, 0, 360));
		$this->addPrimarySystem(new PlasmaBattery(2, 2, 0, 2));	
		$this->addPrimarySystem(new CargoBay(2, 8, 0, 0));			
		
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
		$this->addFrontSystem(new MediumPlasma(2, 5, 3, 240, 60));
		$this->addFrontSystem(new MediumPlasma(2, 5, 3, 300, 120));		

		
        $this->addAftSystem(new Thruster(3, 5, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 5, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 5, 0, 3, 2));        
		$this->addAftSystem(new MediumPlasma(2, 5, 3, 120, 300));	
		$this->addAftSystem(new MediumPlasma(2, 5, 3, 60, 240));			
       
        $this->addPrimarySystem(new Structure( 3, 35));
        
        $this->hitChart = array(
        		0=> array(
        				7 => "Thruster",
						8 => "Cargo Bay",
						11 => "Plasma Web",
						12 => "Plasma Battery",
						14 => "Scanner",
						16 => "Engine",
        				17 => "Hangar",
						19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				5 => "Thruster",
        				8 => "Medium Plasma Cannon",
        				17 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				9 => "Medium Plasma Cannon",        				
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
?>
