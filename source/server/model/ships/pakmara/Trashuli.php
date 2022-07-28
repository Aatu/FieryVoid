<?php
class Trashuli extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 315;
		$this->faction = "Pak'ma'ra";
        $this->phpclass = "Trashuli";
        $this->imagePath = "img/ships/PakmaraTrashuli.png";
        $this->shipClass = "Tra'shu'li Armed Liner";
        $this->canvasSize = 100;

        $this->isd = 2195;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
		$this->iniativebonus = 11*5;
		
        $this->addPrimarySystem(new Reactor(3, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 6, 4, 5));
		$this->addPrimarySystem(new PakmaraCnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Engine(3, 11, 0, 10, 2));
        $this->addPrimarySystem(new Hangar(2, 4));
		$this->addPrimarySystem(new Thruster(4, 10, 0, 4, 3));
		$this->addPrimarySystem(new Thruster(4, 10, 0, 4, 4));
		$this->addPrimarySystem(new Quarters(2, 9));
		$this->addPrimarySystem(new Quarters(2, 9));	
		$this->addPrimarySystem(new CargoBay(2, 8));			
		
        $this->addFrontSystem(new Thruster(3, 8, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 4, 1));
		$this->addFrontSystem(new PlasmaBattery(2, 2, 0, 2));
		$this->addFrontSystem(new HeavyPlasma(3, 8, 5, 300, 60));	                
		$this->addFrontSystem(new MediumPlasma(2, 5, 3, 240, 60));
		$this->addFrontSystem(new MediumPlasma(2, 5, 3, 300, 120));		
		
        $this->addAftSystem(new Thruster(2, 10, 0, 5, 2));
        $this->addAftSystem(new Thruster(2, 10, 0, 5, 2));
		$this->addAftSystem(new PakmaraPlasmaWeb(2, 4, 2, 0, 360));   
		$this->addAftSystem(new MediumPlasma(2, 5, 3, 90, 270));				
       
        $this->addPrimarySystem(new Structure( 3, 54));
        
        $this->hitChart = array(
        		0=> array(
        				7 => "Thruster",
						9 => "Cargo Bay",
						12 => "Quarters",
						14 => "Scanner",
						16 => "Engine",
        				17 => "Hangar",
						19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				5 => "Thruster",
                        6 => "Plasma Battery",
        				7 => "Heavy Plasma Cannon",                      		
        				9 => "Medium Plasma Cannon",
        				17 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				7 => "Medium Plasma Cannon",
                        8 => "Plasma Web",        				        		
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
?>
