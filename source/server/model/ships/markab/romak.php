<?php
class Romak extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 400;
		$this->faction = "Markab";
        $this->phpclass = "Romak";
        $this->imagePath = "img/ships/MarkabPoliceShip.png"; //change
        $this->shipClass = "Romak Escort Frigate";
        //$this->canvasSize = 100;
	    
	    $this->isd = 2000;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.5;
        $this->accelcost = 1;
        $this->rollcost = 2;
        $this->pivotcost = 1;
		$this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(3, 13, 0, 0));
        $this->addPrimarySystem(new CnC(4, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 5, 5));
        $this->addPrimarySystem(new Engine(3, 9, 0, 4, 2));
		$this->addPrimarySystem(new Hangar(3, 2));
		$this->addPrimarySystem(new Thruster(3, 10, 0, 3, 3));
		$this->addPrimarySystem(new Thruster(3, 10, 0, 3, 4));
		
        $this->addFrontSystem(new Thruster(3, 5, 0, 1, 1));
        $this->addFrontSystem(new Thruster(3, 5, 0, 1, 1));
        $this->addFrontSystem(new ScatterGun(2, 0, 0, 240, 0));
        $this->addFrontSystem(new ScatterGun(2, 0, 0, 0, 120));
        $this->addFrontSystem(new ScatterGun(2, 0, 0, 270, 90));
        
        $this->addAftSystem(new Thruster(3, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 2, 2));
        $this->addAftSystem(new ScatterGun(2, 0, 0, 180, 300));
        $this->addAftSystem(new ScatterGun(2, 0, 0, 60, 180));
        
        $this->addPrimarySystem(new Structure( 4, 40));
        
		$this->hitChart = array(
                0=> array(
                        11 => "Thruster",
                        13 => "Scanner",
                        15 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        5 => "Thruster",
                        9 => "Scattergun",
                        17 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        9 => "Scattergun",
                        17 => "Structure",
                        20 => "Primary",
                ),
        );

    }

}



?>
