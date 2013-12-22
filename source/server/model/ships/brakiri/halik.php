<?php
class Halik extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 450;
	$this->faction = "Brakiri";
        $this->phpclass = "Halik";
        $this->imagePath = "img/ships/halik.png";
        $this->shipClass = "Halik Fighter-Killer";
        $this->agile = true;
        $this->gravitic = true;
        $this->canvasSize = 100;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
	$this->iniativebonus = 60;

        $this->gravitic = true;
        
        $this->addPrimarySystem(new Reactor(4, 12, 0, 0));
        $this->addPrimarySystem(new CnC(6, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 11, 5, 7));
        $this->addPrimarySystem(new Engine(4, 9, 0, 10, 2));
	$this->addPrimarySystem(new Hangar(3, 1));
	$this->addPrimarySystem(new GraviticThruster(4, 10, 0, 5, 3));
	$this->addPrimarySystem(new GraviticThruster(4, 10, 0, 5, 4));
		
        $this->addFrontSystem(new GravitonPulsar(3, 6, 2, 180, 0));
        $this->addFrontSystem(new GravitonPulsar(3, 6, 2, 270, 90));
        $this->addFrontSystem(new GraviticThruster(4, 8, 0, 2, 1));
        $this->addFrontSystem(new GraviticThruster(4, 8, 0, 2, 1));
        $this->addFrontSystem(new GravitonPulsar(3, 6, 2, 270, 90));
        $this->addFrontSystem(new GravitonPulsar(3, 6, 2, 0, 180));
        $this->addFrontSystem(new GravitonPulsar(3, 6, 2, 240, 60));
        $this->addFrontSystem(new GravitonPulsar(3, 6, 2, 300, 120));
        
        $this->addAftSystem(new GraviticThruster(4, 10, 0, 5, 2));
        $this->addAftSystem(new GraviticThruster(4, 10, 0, 5, 2));
        $this->addAftSystem(new GravitonPulsar(3, 6, 2, 90, 270));
        $this->addAftSystem(new GravitonPulsar(3, 6, 2, 90, 270));
		
        $this->addPrimarySystem(new Structure( 4, 54));
    }
}
?>
