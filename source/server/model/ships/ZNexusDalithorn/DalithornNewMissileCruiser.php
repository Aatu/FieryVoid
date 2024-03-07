<?php
class DalithornNewMissileCruiser extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 475;
        $this->faction = "ZNexus Dalithorn Commonwealth";
        $this->phpclass = "DalithornNewMissileCruiser";
        $this->imagePath = "img/ships/Nexus/DalithornNewMissileCruiser.png";
		$this->canvasSize = 125; //img has 200px per side
        $this->shipClass = "New Missile Cruiser";
			$this->variantOf = "Light Cruiser";
			$this->occurence = "rare";
		$this->unofficial = true;
        $this->isd = 2115;

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
        $this->addPrimarySystem(new Scanner(4, 14, 5, 6));
        $this->addPrimarySystem(new Engine(4, 16, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(1, 2));
		$this->addPrimarySystem(new Magazine(3, 12));
		$this->addPrimarySystem(new Catapult(1, 6));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 4));
      
        $this->addFrontSystem(new Thruster(3, 10, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 2, 1));
        $this->addFrontSystem(new NexusLaserMissile(2, 6, 1, 300, 60));
        $this->addFrontSystem(new NexusAutocannon(2, 4, 1, 240, 360));
        $this->addFrontSystem(new NexusAutocannon(2, 4, 1, 0, 120));
        $this->addFrontSystem(new NexusProtector(2, 4, 1, 180, 60));
        $this->addFrontSystem(new NexusProtector(2, 4, 1, 300, 180));
                
        $this->addAftSystem(new Thruster(2, 4, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 13, 0, 4, 2));
        $this->addAftSystem(new Thruster(2, 4, 0, 2, 2));
        $this->addAftSystem(new NexusLaserMissile(2, 6, 1, 300, 60));
        $this->addAftSystem(new NexusLaserMissile(2, 6, 1, 300, 60));
        $this->addAftSystem(new NexusMinigun(2, 4, 1, 120, 360));
        $this->addAftSystem(new NexusMinigun(2, 4, 1, 0, 240));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 40));
        $this->addAftSystem(new Structure( 4, 32));
        $this->addPrimarySystem(new Structure( 4, 40));
		
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
					9 => "Catapult",
					11 => "Magazine",
                    13 => "Thruster",
                    15 => "Scanner",
                    17 => "Engine",
                    18 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
                    7 => "Laser Missile",
                    9 => "Autocannon",
					11 => "Protector",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
					8 => "Minigun",
                    12 => "Laser Missile",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
