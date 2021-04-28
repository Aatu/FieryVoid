<?php
class DalithornFlagCruiserRefit extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 375;
        $this->faction = "ZNexus Dalithorn";
        $this->phpclass = "DalithornFlagCruiserRefit";
        $this->imagePath = "img/ships/Nexus/DalithornFlagCruiser.png";
		$this->canvasSize = 115; //img has 200px per side
        $this->shipClass = "Flag Cruiser (2048 Refit)";
			$this->variantOf = "Flag Cruiser";
			$this->occurence = "common";
		$this->unofficial = true;
        $this->isd = 2048;

        $this->fighters = array("superheavy"=>1);
        
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 6*5;
        
        $this->addPrimarySystem(new Reactor(3, 16, 0, 0));
        $this->addPrimarySystem(new CnC(3, 14, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 14, 5, 5));
        $this->addPrimarySystem(new Engine(3, 16, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(1, 2));
		$this->addPrimarySystem(new CargoBay(2, 12));
		$this->addPrimarySystem(new Catapult(1, 6));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 4, 4));
      
        $this->addFrontSystem(new Thruster(2, 10, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 10, 0, 2, 1));
        $this->addFrontSystem(new NexusCoilgun(2, 10, 4, 330, 30));
        $this->addFrontSystem(new NexusLightGasGun(2, 5, 1, 240, 360));
        $this->addFrontSystem(new NexusLightGasGun(2, 5, 1, 0, 120));
        $this->addFrontSystem(new NexusShatterGun(1, 2, 1, 180, 60));
        $this->addFrontSystem(new NexusShatterGun(1, 2, 1, 300, 180));
                
        $this->addAftSystem(new Thruster(1, 4, 0, 2, 2));
        $this->addAftSystem(new Thruster(1, 4, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 13, 0, 4, 2));
        $this->addAftSystem(new NexusGasGun(2, 7, 2, 300, 60));
        $this->addAftSystem(new NexusGasGun(2, 7, 2, 300, 60));
        $this->addAftSystem(new NexusLightGasGun(2, 5, 1, 120, 300));
        $this->addAftSystem(new NexusLightGasGun(2, 5, 1, 60, 240));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 40));
        $this->addAftSystem(new Structure( 3, 32));
        $this->addPrimarySystem(new Structure( 4, 40));
		
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
					9 => "Catapult",
					11 => "Cargo Bay",
                    13 => "Thruster",
                    15 => "Scanner",
                    17 => "Engine",
                    18 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
                    7 => "Coilgun",
                    9 => "Light Gas Gun",
					11 => "Shatter Gun",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
					8 => "Light Gas Gun",
                    10 => "Gas Gun",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
