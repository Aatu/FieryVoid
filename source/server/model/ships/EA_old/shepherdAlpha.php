<?php
class ShepherdAlpha extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 150;
	$this->faction = 'EA (early)';//"EA defenses";
        $this->phpclass = "ShepherdAlpha";
        $this->imagePath = "img/ships/shepherd.png";
        $this->shipClass = "Shepherd Fighter Transport (Alpha)";
//			$this->variantOf = "Laertes Police Corvette (Gamma)";
//			$this->occurence = "common";
        $this->canvasSize = 80;
 		$this->unofficial = true;

		$this->fighters = array("normal"=>6); 
        
        $this->isd = 2138;

        $this->forwardDefense = 14;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
		$this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(3, 10, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 2, 4));
		$this->addPrimarySystem(new Hangar(3, 1));
		$this->addPrimarySystem(new Thruster(3, 8, 0, 2, 3));
		$this->addPrimarySystem(new Thruster(3, 8, 0, 2, 4));
		
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
		$this->addFrontSystem(new Hangar(0, 6));
        $this->addFrontSystem(new LtBlastCannon(2, 4, 1, 270, 90));
		
        $this->addAftSystem(new Thruster(3, 6, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 6, 0, 2, 2));
        $this->addAftSystem(new Engine(3, 11, 0, 4, 2));
        $this->addAftSystem(new LtBlastCannon(2, 4, 1, 180, 360));
        $this->addAftSystem(new LtBlastCannon(2, 4, 1, 0, 180));
	
        $this->addPrimarySystem(new Structure( 3, 40));

        $this->hitChart = array(
            0=> array(
                    11 => "Thruster",
                    14 => "Scanner",
					16 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    5 => "Thruster",
					8 => "Hangar",
                    10 => "Light Blast Cannon",
					17 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
					8 => "Light Blast Cannon",
                    10 => "Engine",
                    17 => "Structure",
                    20 => "Primary",
            ),
        );		
    }

}



?>
