<?php
class JYDLias extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 450;
		$this->faction = 'Raiders';
		$this->phpclass = "JYDLias";
		$this->imagePath = "img/ships/GaimTiac.png";
		$this->shipClass = "JYD Converted Lias Supply Ship";
		$this->shipSizeClass = 3;
			$this->occurence = "unique";

        $this->fighters = array("light"=>12);
	    
		$this->notes = 'Used only by the Junkyard Dogs';
		$this->notes .= '<br>Only one exists';
        $this->isd = 2259;

		$this->forwardDefense = 14;
		$this->sideDefense = 15;

		$this->turncost = 1;
		$this->turndelaycost = 1;
		$this->accelcost = 4;
		$this->rollcost = 4;
		$this->pivotcost = 4;

		$this->iniativebonus = 0;

		$this->addPrimarySystem(new Reactor(5, 18, 0, 0));
		$this->addPrimarySystem(new CnC(5, 12, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 12, 5, 7));
		$this->addPrimarySystem(new Engine(5, 23, 0, 10, 4));
		$this->addPrimarySystem(new Hangar(5, 3));

		$this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
		$this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
		$this->addFrontSystem(new MediumPulse(5, 6, 3, 300, 60));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 180, 60));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 300, 180));
		$this->addFrontSystem(new CargoBay(4, 28));
		$this->addFrontSystem(new CargoBay(4, 28));

		$this->addAftSystem(new Thruster(4, 13, 0, 5, 2));
		$this->addAftSystem(new Thruster(4, 13, 0, 5, 2));
		$this->addAftSystem(new TwinArray(3, 6, 2, 120, 360));
		$this->addAftSystem(new TwinArray(3, 6, 2, 0, 240));
		$this->addAftSystem(new JumpEngine(4, 20, 5, 48));

		$this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));
		$this->addLeftSystem(new LightParticleCannon(4, 6, 5, 240, 60));

		$this->addRightSystem(new Thruster(4, 15, 0, 5, 4));
		$this->addRightSystem(new LightParticleCannon(4, 6, 5, 300, 120));
        
        $this->addFrontSystem(new Structure( 5, 44));
        $this->addAftSystem(new Structure( 5, 44));
        $this->addLeftSystem(new Structure( 5, 54));
        $this->addRightSystem(new Structure( 5, 54));
        $this->addPrimarySystem(new Structure( 5, 44));
		
		
		$this->hitChart = array(
                0=> array(
                        9 => "Structure",
                        12 => "Scanner",
                        15 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        3 => "Thruster",
                        5 => "Twin Array",
						6 => "Medium Pulse Cannon",
						10 => "Cargo Bay",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        7 => "Thruster",
                        9 => "Jump Engine",
						11 => "Twin Array",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
						6 => "Light Particle Cannon",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
						6 => "Light Particle Cannon",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );

    }
}
?>
