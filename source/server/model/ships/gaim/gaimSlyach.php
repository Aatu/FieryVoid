<?php
class gaimSlyach extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 450;
		$this->faction = "Gaim";
        $this->phpclass = "gaimSlyach";
        $this->imagePath = "img/ships/GaimSlyach.png";
        $this->shipClass = "Slyach Frigate";
        $this->canvasSize = 100;

        $this->agile = true;
        $this->isd = 2258;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
		$this->iniativebonus = 60;
		
        $this->addPrimarySystem(new Reactor(4, 12, 0, 3));
        $this->addPrimarySystem(new Scanner(4, 13, 5, 8));
		$this->addPrimarySystem(new CnC(5, 9, 0, 0));
        $this->addPrimarySystem(new Hangar(3, 1));
        $this->addPrimarySystem(new Engine(4, 12, 0, 9, 2));
		$this->addPrimarySystem(new Thruster(3, 13, 0, 5, 3));
		$this->addPrimarySystem(new Thruster(3, 13, 0, 5, 4));
		
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new TwinArray(2, 6, 2, 180, 60));
        $this->addFrontSystem(new TwinArray(2, 6, 2, 300, 180));
		$this->addFrontSystem(new BattleLaser(3, 6, 6, 300, 60));
		$this->addFrontSystem(new Bulkhead(0, 2));
		
        $this->addAftSystem(new Thruster(3, 8, 0, 5, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 5, 2));
        $this->addAftSystem(new ScatterGun(2, 8, 3, 180, 360));
        $this->addAftSystem(new ScatterGun(2, 8, 3, 0, 180));
		$this->addAftSystem(new Bulkhead(0, 2));
       
        $this->addPrimarySystem(new Structure( 4, 54));
        
        $this->hitChart = array(
        		0=> array(
        				9 => "Thruster",
						12 => "Scanner",
						15 => "Engine",
						16 => "Hangar",
        				18 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				5 => "Thruster",
        				7 => "Battle Laser",
        				10 => "Twin Array",
        				16 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				8 => "Scattergun",
        				16 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
?>
