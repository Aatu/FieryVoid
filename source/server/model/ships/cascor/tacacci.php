<?php
class Tacacci extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 440;
		$this->faction = "Cascor";
        $this->phpclass = "Tacacci";
        $this->imagePath = "img/ships/CascorTaccaci.png";
        $this->shipClass = "Tacacci Strike Frigate";
        $this->canvasSize = 100;
	    $this->agile = true;
	    $this->isd = 2218;
	    $this->fighters = array("ultralight"=>12);
	           
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.25;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 2;
		$this->iniativebonus = 65;
         
        $this->addPrimarySystem(new Reactor(3, 13, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 3, 6));
        $this->addPrimarySystem(new Engine(3, 14, 0, 6, 4));
		$this->addPrimarySystem(new Hangar(3, 7));
		$this->addPrimarySystem(new Thruster(3, 16, 0, 4, 3));
		$this->addPrimarySystem(new Thruster(3, 16, 0, 4, 4));
		
        $this->addFrontSystem(new Thruster(3, 15, 0, 4, 1));
        $this->addFrontSystem(new IonTorpedo(3, 5, 4, 240, 360));
        $this->addFrontSystem(new IonCannon(3, 6, 4, 240, 120));
        $this->addFrontSystem(new DualIonBolter(2, 4, 4, 240, 120));
        $this->addFrontSystem(new IonTorpedo(3, 5, 4, 0, 120));
		
        $this->addAftSystem(new Thruster(3, 13, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 13, 0, 3, 2));
        $this->addAftSystem(new DualIonBolter(2, 4, 4, 60, 300));
	
        $this->addPrimarySystem(new Structure( 4, 60));
        
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
                        6 => "Thruster",
                        7 => "Ion Cannon",
                        9 => "Ion Torpedo",
                        10 => "Dual Ion Bolter",
                        17 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        8 => "Dual Ion Bolter",
                        17 => "Structure",
                        20 => "Primary",
                ),
        );

    }

}



?>
