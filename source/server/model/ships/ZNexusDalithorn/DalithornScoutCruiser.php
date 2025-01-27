<?php
class DalithornScoutCruiser extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 490;
        $this->faction = "ZNexus Dalithorn Commonwealth";
        $this->phpclass = "DalithornScoutCruiser";
        $this->imagePath = "img/ships/Nexus/Dalithorn_Scout2.png";
		$this->canvasSize = 125; //img has 200px per side
        $this->shipClass = "Scout Cruiser";
		$this->unofficial = true;
        $this->limited = 10;
        $this->isd = 2125;

        $this->fighters = array("superheavy"=>1);
        
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 6*5;
        
        $this->addPrimarySystem(new Reactor(4, 16, 0, 0));
        $this->addPrimarySystem(new CnC(4, 14, 0, 0));
        $this->addPrimarySystem(new ELINTScanner(4, 14, 5, 6));
        $this->addPrimarySystem(new Engine(4, 16, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(1, 2));
		$this->addPrimarySystem(new Magazine(3, 12));
		$this->addPrimarySystem(new Catapult(1, 6));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 4));
      
        $this->addFrontSystem(new Thruster(3, 10, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 2, 1));
        $this->addFrontSystem(new ELINTScanner(3, 10, 5, 4));
        $this->addFrontSystem(new NexusAutocannon(2, 4, 1, 240, 360));
        $this->addFrontSystem(new NexusAutocannon(2, 4, 1, 0, 120));
        $this->addFrontSystem(new NexusProtector(2, 4, 1, 180, 60));
        $this->addFrontSystem(new NexusProtector(2, 4, 1, 300, 180));
                
        $this->addAftSystem(new Thruster(2, 4, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 13, 0, 4, 2));
        $this->addAftSystem(new Thruster(2, 4, 0, 2, 2));
        $this->addAftSystem(new NexusAutocannon(2, 4, 1, 300, 60));
        $this->addAftSystem(new NexusAutocannon(2, 4, 1, 300, 60));
        $this->addAftSystem(new NexusMinigun(2, 4, 1, 120, 360));
        $this->addAftSystem(new NexusMinigun(2, 4, 1, 0, 240));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 40));
        $this->addAftSystem(new Structure( 4, 32));
        $this->addPrimarySystem(new Structure( 4, 40));
		
        $this->hitChart = array(
            0=> array(
                    6 => "Structure",
					7 => "Catapult",
					8 => "Magazine",
                    12 => "Thruster",
                    14 => "ELINT Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
                    7 => "ELINT Scanner",
                    9 => "Autocannon",
					11 => "Protector",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
					8 => "Minigun",
                    10 => "Autocannon",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
