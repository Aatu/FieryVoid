<?php
class MakarDeskoron extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 180;
		$this->faction = "Nexus Makar Federation (early)";
        $this->phpclass = "MakarDeskoron";
        $this->imagePath = "img/ships/civilianFreighter.png";
        $this->shipClass = "Deskoron Armed Freighter";
        $this->canvasSize = 100;
		$this->unofficial = true;
		$this->isd = 1921;

        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 999;
        $this->pivotcost = 999;
		$this->iniativebonus = 0;
		
        $this->addPrimarySystem(new Reactor(3, 8, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 3, 3));
        $this->addPrimarySystem(new Engine(3, 6, 0, 6, 4));
		$this->addPrimarySystem(new Hangar(3, 4, 2));
		$this->addPrimarySystem(new PlasmaTorch(1, 4, 2, 0, 360));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 6, 3));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 6, 4));
		
        $this->addFrontSystem(new Thruster(2, 15, 0, 6, 1));
		$this->addFrontSystem(new EWRocketLauncher(3, 4, 1, 240, 60));
		$this->addFrontSystem(new EWRocketLauncher(3, 4, 1, 300, 120));
		
        $this->addAftSystem(new Thruster(2, 15, 0, 6, 2));
		$this->addAftSystem(new NexusDefenseGun(2, 4, 1, 180, 360));
		$this->addAftSystem(new NexusDefenseGun(2, 4, 1, 180, 360));
		$this->addAftSystem(new NexusDefenseGun(2, 4, 1, 0, 180));
		$this->addAftSystem(new NexusDefenseGun(2, 4, 1, 0, 180));
       
        $this->addPrimarySystem(new Structure( 3, 48));
        
        $this->hitChart = array(
        		0=> array(
        				7 => "Thruster",
        				9 => "Plasma Torch",
        				12 => "Scanner",
        				15 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				4 => "Thruster",
        				8 => "Rocket Launcher",
        				17 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				4 => "Thruster",
        				8 => "Defense Gun",
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
?>
