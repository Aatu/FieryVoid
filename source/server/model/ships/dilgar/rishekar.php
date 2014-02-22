<?php
class Rishekar extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 275;
	$this->faction = "Dilgar";
        $this->phpclass = "Rishekar";
        $this->imagePath = "img/ships/haven.png";
        $this->shipClass = "Rishekar Early Frigate";
        $this->canvasSize = 100;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 13;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.66;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
	$this->iniativebonus = 60;

        $this->addPrimarySystem(new LightLaser(1, 4, 3, 240, 360));
        $this->addPrimarySystem(new LightLaser(1, 4, 3, 0, 120));
        $this->addPrimarySystem(new Reactor(4, 12, 0, 0));
        $this->addPrimarySystem(new CnC(4, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 3, 5));
        $this->addPrimarySystem(new Engine(4, 9, 0, 4, 2));
	$this->addPrimarySystem(new Hangar(2, 1));
	$this->addPrimarySystem(new Thruster(2, 8, 0, 3, 3));
	$this->addPrimarySystem(new Thruster(2, 8, 0, 3, 4));
		
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 240, 360));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 0, 120));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        
        $this->addAftSystem(new Thruster(2, 8, 0, 3, 2));
        $this->addAftSystem(new Engine(2, 4, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 8, 0, 3, 2));
        $this->addAftSystem(new LightPlasma(1, 4, 2, 180, 300));
        $this->addAftSystem(new LightPlasma(1, 4, 2, 60, 180));
		
        $this->addPrimarySystem(new Structure( 4, 40));
    }
}
?>
