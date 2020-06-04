<?php
class MarkabLiner extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 70;
		$this->faction = "Civilians";
        $this->phpclass = "MarkabLiner";
        $this->imagePath = "img/ships/sloop.png"; //change
        $this->shipClass = "Markab Liner";
        $this->canvasSize = 100;
	    
	    $this->isd = 2120;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 0;
		$this->iniativebonus = -10;
         
        $this->addPrimarySystem(new Reactor(3, 10, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 3, 3));
        $this->addPrimarySystem(new Engine(3, 12, 0, 4, 2));
		$this->addPrimarySystem(new Hangar(3, 4));
		$this->addPrimarySystem(new Thruster(3, 9, 0, 4, 3));
		$this->addPrimarySystem(new Thruster(3, 9, 0, 4, 4));
		$this->addPrimarySystem(new CargoBay(3, 36));
		$this->addPrimarySystem(new CargoBay(3, 36));
		
        $this->addFrontSystem(new Thruster(3, 9, 0, 4, 1));
        
        $this->addAftSystem(new Thruster(3, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 2, 2));
        
        $this->addPrimarySystem(new Structure( 3, 48));
        
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
                        9 => "0:Cargo Bay",
                        17 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        9 => "0:Cargo Bay",
                		17 => "Structure",
                        20 => "Primary",
                ),
        );

    }

}



?>
