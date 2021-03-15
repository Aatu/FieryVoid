<?php
class LaertesBeta extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 275;
	$this->faction = 'EA';//"EA defenses";
        $this->phpclass = "LaertesBeta";
        $this->imagePath = "img/ships/laertes.png";
        $this->shipClass = "Laertes Police Corvette (Beta)";
			$this->variantOf = "Laertes Police Corvette (Gamma)";
			$this->occurence = "common";
        $this->canvasSize = 100;
 		$this->unofficial = true;
        
        $this->isd = 2154;

        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 2;
		$this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(3, 13, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 3, 4));
		$this->addPrimarySystem(new Hangar(3, 2));
		$this->addPrimarySystem(new Thruster(3, 8, 0, 2, 3));
		$this->addPrimarySystem(new Thruster(3, 8, 0, 2, 4));
		
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
        $this->addFrontSystem(new LtBlastCannon(2, 4, 1, 240, 60));
        $this->addFrontSystem(new LtBlastCannon(2, 4, 1, 300, 120));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
		
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
					8 => "Medium Plasma Cannon",
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
