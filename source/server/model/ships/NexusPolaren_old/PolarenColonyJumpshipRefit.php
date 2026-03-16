<?php
class PolarenColonyJumpshipRefit extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 725;
		$this->faction = "Nexus Polaren Confederacy (early)";
        $this->phpclass = "PolarenColonyJumpshipRefit";
        $this->imagePath = "img/ships/Nexus/polarenColonyJumpship.png";
			$this->canvasSize = 200; //img has 200px per side
        $this->shipClass = "Colony Jumpship (refit)";
			$this->variantOf = "Colony Jumpship";
			$this->occurence = "common";
		$this->limited = 10;
		$this->unofficial = true;
		$this->isd = 2121;
         
        $this->fighters = array("assault shuttles"=>4); //2 breaching pods    
		
        $this->forwardDefense = 17;
        $this->sideDefense = 19;
        
        $this->turncost = 1.66;
        $this->turndelaycost = 1.66;
        $this->accelcost = 6;
        $this->rollcost = 3;
        $this->pivotcost = 5;
		$this->iniativebonus = -4;
         
        $this->addPrimarySystem(new Reactor(4, 35, 0, 0));
		$this->addPrimarySystem(new FlagBridge(4, 28, 0, 1, 'Polaren Command Bonus', 60,  true, true, true, false, array("Nexus Polaren Confederacy (early)", "Nexus Polaren Confederacy (early)")));
        $this->addPrimarySystem(new ELINTScanner(3, 25, 7, 10));
		$this->addPrimarySystem(new Hangar(2, 10));
		$this->addPrimarySystem(New JumpEngine(2, 30, 9, 50));

        $this->addFrontSystem(new Thruster(2, 20, 0, 4, 1));
        $this->addFrontSystem(new Thruster(2, 20, 0, 4, 1));
		$this->addFrontSystem(new Maser(2, 6, 3, 240, 60));
		$this->addFrontSystem(new NexusHeavyMaser(3, 7, 4, 240, 360));
		$this->addFrontSystem(new NexusHeavyMaser(3, 7, 4, 0, 120));
		$this->addFrontSystem(new Maser(2, 6, 3, 300, 120));

        $this->addAftSystem(new Engine(3, 30, 0, 16, 5));
		$this->addAftSystem(new Thruster(2, 24, 0, 5, 2));
		$this->addAftSystem(new Thruster(2, 24, 0, 6, 2));
		$this->addAftSystem(new Thruster(2, 24, 0, 5, 2));
		$this->addAftSystem(new Maser(2, 6, 3, 120, 300));
		$this->addAftSystem(new NexusHeavyMaser(3, 7, 4, 180, 300));
		$this->addAftSystem(new NexusHeavyMaser(3, 7, 4, 60, 180));
		$this->addAftSystem(new Maser(2, 6, 3, 60, 240));
		$this->addAftSystem(new CargoBay(2, 35));
		$this->addAftSystem(new CargoBay(2, 35));
        
		$this->addLeftSystem(new Thruster(2, 20, 0, 8, 3));
		$this->addLeftSystem(new Maser(1, 6, 3, 180, 360));
		$this->addLeftSystem(new Maser(1, 6, 3, 180, 360));
		$this->addLeftSystem(new NexusSandCaster(1, 4, 2, 180, 360));
		$this->addLeftSystem(new DockingCollar(2, 10));
		$this->addLeftSystem(new DockingCollar(2, 10));
		$this->addLeftSystem(new DockingCollar(2, 10));
		
		$this->addRightSystem(new Thruster(2, 20, 0, 8, 4));
		$this->addRightSystem(new Maser(1, 6, 3, 0, 180));
		$this->addRightSystem(new Maser(1, 6, 3, 0, 180));
		$this->addRightSystem(new NexusSandCaster(1, 4, 2, 0, 180));
		$this->addRightSystem(new DockingCollar(2, 10));
		$this->addRightSystem(new DockingCollar(2, 10));
		$this->addRightSystem(new DockingCollar(2, 10));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 54));
        $this->addAftSystem(new Structure( 3, 48));
        $this->addLeftSystem(new Structure( 3, 60));
        $this->addRightSystem(new Structure( 3, 60));
        $this->addPrimarySystem(new Structure( 3, 54));
		
        $this->hitChart = array(
            0=> array(
                    9 => "Structure",
					12 => "Jump Engine",
					15 => "ELINT Scanner",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    6 => "Thruster",
					8 => "Heavy Maser",
					10 => "Maser",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array( //Aft
                    5 => "Thruster",
                    8 => "Cargo Bay",
					10 => "Engine",
                    11 => "Maser",
                    12 => "Heavy Maser",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    5 => "Thruster",
					7 => "Maser",
					8 => "Sand Caster",
					11 => "Docking Collar",
                    18 => "Structure",
                    20 => "Primary",
			),
            4=> array(
                    5 => "Thruster",
					7 => "Maser",
					8 => "Sand Caster",
					11 => "Docking Collar",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );

		
    }
}
?>