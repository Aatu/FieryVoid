<?php
class Urikhal extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 445;
		$this->faction = "Pak'ma'ra";
        $this->phpclass = "Urikhal";
        $this->imagePath = "img/ships/PakmaraUrikhal.png";
        $this->shipClass = "Urik'hal Fast Destroyer";
        $this->canvasSize = 100;

        $this->isd = 2252;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 3;
		$this->iniativebonus = 11*5;
		
        $this->addPrimarySystem(new Reactor(4, 15, 0, 2));
        $this->addPrimarySystem(new Scanner(4, 10, 5, 6));
		$this->addPrimarySystem(new PakmaraCnC(5, 8, 0, 0));
        $this->addPrimarySystem(new Engine(4, 14, 0, 14, 2));
        $this->addPrimarySystem(new Hangar(2, 2));
		$this->addPrimarySystem(new Thruster(4, 13, 0, 7, 3));
		$this->addPrimarySystem(new Thruster(4, 13, 0, 7, 4));	
		$this->addPrimarySystem(new CargoBay(2, 8));			
		
        $this->addFrontSystem(new Thruster(3, 8, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 4, 1));
		$this->addFrontSystem(new PlasmaBattery(2, 2, 0, 2));
		$this->addFrontSystem(new DualPlasmaStream(3, 10, 10, 300, 60));	                
		$this->addFrontSystem(new HeavyPlasma(3, 8, 5, 240, 60));
		$this->addFrontSystem(new HeavyPlasma(3, 8, 5, 300, 120));		
		
        $this->addAftSystem(new Thruster(2, 11, 0, 7, 2));
        $this->addAftSystem(new Thruster(2, 11, 0, 7, 2));
		$this->addAftSystem(new PakmaraPlasmaWeb(2, 4, 2, 0, 360));   
		$this->addAftSystem(new MediumPlasma(2, 5, 3, 180, 360));
		$this->addAftSystem(new MediumPlasma(2, 5, 3, 0, 180));						
       
        $this->addPrimarySystem(new Structure( 4, 60));
        
        $this->hitChart = array(
        		0=> array(
        				9 => "Thruster",
						12 => "Cargo Bay",
						14 => "Scanner",
						16 => "Engine",
        				17 => "Hangar",
						19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				5 => "Thruster",
                        6 => "Plasma Battery",
        				8 => "Heavy Plasma Cannon",                      		
        				10 => "Dual Plasma Stream",
        				17 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				5 => "Thruster",
        				7 => "Medium Plasma Cannon",
                        8 => "Plasma Web",        				        		
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
?>
