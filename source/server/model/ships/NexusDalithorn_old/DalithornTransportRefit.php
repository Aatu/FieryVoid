<?php
class DalithornTransportRefit extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 265;
        $this->faction = "Nexus Support Units";
        $this->phpclass = "DalithornTransportRefit";
        $this->imagePath = "img/ships/Nexus/Dalithorn_Transport2.png";
		$this->canvasSize = 125; //img has 200px per side
        $this->shipClass = "Dalithorn Military Transport (2048)";
			$this->variantOf = "Dalithorn Military Transport";
			$this->occurence = "common";
		$this->unofficial = true;
        $this->isd = 2048;

        $this->fighters = array("superheavy"=>1);
        
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.75;
        $this->turndelaycost = 0.75;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 3*5;
        
        $this->addPrimarySystem(new Reactor(3, 10, 0, 0));
        $this->addPrimarySystem(new CnC(3, 10, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 14, 3, 4));
        $this->addPrimarySystem(new Engine(3, 16, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(1, 2));
		$this->addPrimarySystem(new Magazine(2, 6));
		$this->addPrimarySystem(new Catapult(1, 6));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 4, 4));
      
        $this->addFrontSystem(new Thruster(2, 10, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 10, 0, 2, 1));
        $this->addFrontSystem(new NexusShatterGun(1, 2, 1, 180, 60));
        $this->addFrontSystem(new NexusLightGasGun(2, 5, 1, 240, 360));
        $this->addFrontSystem(new NexusLightGasGun(2, 5, 1, 300, 60));
        $this->addFrontSystem(new NexusLightGasGun(2, 5, 1, 0, 120));
        $this->addFrontSystem(new NexusShatterGun(1, 2, 1, 300, 180));
                
        $this->addAftSystem(new Thruster(1, 4, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 13, 0, 4, 2));
        $this->addAftSystem(new Thruster(1, 4, 0, 2, 2));
		$this->addAftSystem(new CargoBay(2, 44));
		$this->addAftSystem(new CargoBay(2, 44));
        $this->addAftSystem(new NexusLightGasGun(2, 5, 1, 120, 300));
        $this->addAftSystem(new NexusLightGasGun(2, 5, 1, 60, 240));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 40));
        $this->addAftSystem(new Structure( 3, 32));
        $this->addPrimarySystem(new Structure( 4, 40));
		
        $this->hitChart = array(
            0=> array(
                    6 => "Structure",
					7 => "Catapult",
					8 => "Magazine",
                    12 => "Thruster",
                    14 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
                    6 => "Light Gas Gun",
					7 => "Shatter Gun",
					11 => "2:Cargo Bay",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
					8 => "Light Gas Gun",
                    12 => "Cargo Bay",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
