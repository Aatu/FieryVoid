<?php
class MakarGethishanRefit extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 510;
        $this->faction = "ZNexus Makar Federation";
        $this->phpclass = "MakarGethishanRefit";
        $this->imagePath = "img/ships/Nexus/makar_niskan2.png";
		$this->canvasSize = 125; //img has 200px per side
        $this->shipClass = "Gethishan Tender (2110)";
			$this->variantOf = "Niskan Half Cruiser (2109)";
			$this->occurence = "uncommon";
		$this->unofficial = true;
        $this->isd = 2050;

		$this->fighters = array("LCVs" => 2);        

        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 30;
         
        $this->addPrimarySystem(new Reactor(4, 20, 0, 0));
        $this->addPrimarySystem(new CnC(4, 18, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 16, 5, 6));
        $this->addPrimarySystem(new Hangar(0, 4));
        $this->addPrimarySystem(new Engine(4, 20, 0, 8, 3));
        $this->addPrimarySystem(new Thruster(3, 15, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 15, 0, 4, 4));
      
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
		$this->addFrontSystem(new NexusLightXRayLaser(2, 3, 1, 270, 90));
		$this->addFrontSystem(new NexusRAMLauncher(3, 8, 4, 300, 60));
		$this->addFrontSystem(new NexusLCVController(5, 10, 5, 1));
		$this->addFrontSystem(new NexusRAMLauncher(3, 8, 4, 300, 60));
		$this->addFrontSystem(new NexusLightXRayLaser(2, 3, 1, 270, 90));
                
        $this->addAftSystem(new Thruster(3, 15, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 4, 2));
		$this->addAftSystem(new DockingCollar(2, 10));
		$this->addAftSystem(new NexusLightXRayLaser(2, 3, 1, 180, 360));
		$this->addAftSystem(new NexusXRayLaser(3, 5, 2, 180, 300));
		$this->addAftSystem(new NexusXRayLaser(3, 5, 2, 60, 180));
		$this->addAftSystem(new NexusLightXRayLaser(2, 3, 1, 0, 180));
		$this->addAftSystem(new DockingCollar(2, 10));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 50));
        $this->addAftSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 5, 48));
		
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
                    11 => "Thruster",
					12 => "Hangar",
                    15 => "Scanner",
                    17 => "Engine",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
                    6 => "RAM Launcher",
                    8 => "Light X-Ray Laser",
					10 => "LCV Controller",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    5 => "Thruster",
					7 => "Docking Collar",
                    9 => "Light X-Ray Laser",
					11 => "X-Ray Laser",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
