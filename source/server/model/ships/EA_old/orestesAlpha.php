<?php
class OrestesAlpha extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 450;
		$this->faction = 'EA (early)';//"EA defenses";
		$this->phpclass = "OrestesAlpha";
		$this->imagePath = "img/ships/orestes.png";
		$this->shipClass = "Orestes System Monitor (Alpha)";
//	        $this->variantOf = "Orestes System Monitor (Epsilon)";
//			$this->occurence = "common";
 		$this->unofficial = true;
        $this->shipSizeClass = 3;
        $this->limited = 10;

		$this->fighters = array("normal"=>6);
	    
	    $this->isd = 2130;

		$this->forwardDefense = 16;
		$this->sideDefense = 16;

		$this->turncost = 1;
		$this->turndelaycost = 1;
		$this->accelcost = 4;
		$this->rollcost = 3;
		$this->pivotcost = 4;

		$this->iniativebonus = -10;

		$this->addPrimarySystem(new Reactor(4, 16, 0, 0));
		$this->addPrimarySystem(new CnC(4, 16, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 14, 2, 4));
		$this->addPrimarySystem(new Engine(4, 11, 0, 4, 4));
		$this->addPrimarySystem(new Hangar(4, 8));
		$this->addPrimarySystem(new LtBlastCannon(3, 4, 1, 0, 360));
		$this->addPrimarySystem(new LtBlastCannon(3, 4, 1, 0, 360));

		$this->addFrontSystem(new Thruster(3, 15, 0, 4, 1));
		$this->addFrontSystem(new EWOMissileRack(3, 6, 0, 240, 60));
		$this->addFrontSystem(new LightLaser(3, 4, 3, 300, 60));
		$this->addFrontSystem(new LightPlasma(2, 4, 2, 270, 90));
		$this->addFrontSystem(new LightLaser(3, 4, 3, 300, 60));
		$this->addFrontSystem(new EWOMissileRack(3, 6, 0, 300, 120));

		$this->addAftSystem(new Thruster(4, 16, 0, 4, 2));
		$this->addAftSystem(new EWOMissileRack(3, 6, 0, 120, 300));
		$this->addAftSystem(new LightPlasma(2, 4, 2, 90, 270));
		$this->addAftSystem(new EWOMissileRack(3, 6, 0, 60, 240));

		$this->addLeftSystem(new Thruster(3, 15, 0, 3, 3));
		$this->addLeftSystem(new MedBlastCannon(3, 5, 2, 180, 360));
		$this->addLeftSystem(new MedBlastCannon(3, 5, 2, 180, 360));

		$this->addRightSystem(new Thruster(3, 15, 0, 3, 4));
		$this->addRightSystem(new MedBlastCannon(3, 5, 2, 0, 180));
		$this->addRightSystem(new MedBlastCannon(3, 5, 2, 0, 180));
        
        $this->addFrontSystem(new Structure( 4, 42));
        $this->addAftSystem(new Structure( 3, 40));
        $this->addLeftSystem(new Structure( 3, 60));
        $this->addRightSystem(new Structure( 3, 60));
        $this->addPrimarySystem(new Structure( 4, 60));
		
		
		$this->hitChart = array(
                0=> array(
                        9 => "Structure",
                        11 => "Light Blast Cannon",
                        13 => "Scanner",
                        15 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        6 => "Light Laser",
                        9 => "Class-O Missile Rack",
                        11 => "Light Plasma Cannon",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        8 => "Class-O Missile Rack",
                        10 => "Light Plasma Cannon",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
                        9 => "Medium Blast Cannon",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
                        9 => "Medium Blast Cannon",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );

    }
}
?>
