<?php
class TethysAlpha extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 275;
		$this->faction = "EA";
        $this->phpclass = "TethysAlpha";
        $this->imagePath = "img/ships/tethys.png";
        $this->shipClass = "Tethys Police Cutter (Alpha)";
			$this->variantOf = "Tethys Police Cutter (Kappa)";
			$this->occurence = "common";
        $this->canvasSize = 100;
		$this->unofficial = true;
	    
	    $this->isd = 2147;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 13;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 1;
		$this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(4, 8, 0, 0));
        $this->addPrimarySystem(new CnC(4, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 2, 3));
        $this->addPrimarySystem(new Engine(4, 11, 0, 6, 2));
		$this->addPrimarySystem(new Hangar(4, 2));
		$this->addPrimarySystem(new Thruster(3, 7, 0, 3, 3));
		$this->addPrimarySystem(new Thruster(3, 7, 0, 3, 4));
		
        $this->addFrontSystem(new Thruster(3, 7, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 7, 0, 3, 1));
        $this->addFrontSystem(new MedBlastCannon(3, 5, 2, 240, 360));
        $this->addFrontSystem(new LtBlastCannon(1, 4, 1, 270, 90));
        $this->addFrontSystem(new LtBlastCannon(1, 4, 1, 270, 90));
        $this->addFrontSystem(new LtBlastCannon(1, 4, 1, 270, 90));
        $this->addFrontSystem(new MedBlastCannon(3, 5, 2, 0, 120));
		
        $this->addAftSystem(new Thruster(4, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 3, 2));
        $this->addAftSystem(new LtBlastCannon(1, 4, 1, 180, 360));
        $this->addAftSystem(new LtBlastCannon(1, 4, 1, 90, 270));
        $this->addAftSystem(new LtBlastCannon(1, 4, 1, 0, 180));
	
        $this->addPrimarySystem(new Structure( 4, 38));
        
		$this->hitChart = array(
                0=> array(
                        9 => "Thruster",
                        11 => "Scanner",
                        14 => "Engine",
                        16 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        6 => "Thruster",
                        8 => "Medium Blast Cannon",
                        12 => "Light Blast Cannon",
                        17 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        8 => "Thruster",
                        10 => "Light Blast Cannon",
                        17 => "Structure",
                        20 => "Primary",
                ),
        );

    }

}



?>
