<?php
class CraytanSorenb extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 240;
        $this->faction = "Nexus Craytan Union";
        $this->phpclass = "CraytanSorenb";
        $this->imagePath = "img/ships/Nexus/craytan_dela.png";
		$this->canvasSize = 125; //img has 200px per side
        $this->shipClass = "Soren Auxiliary Cruiser";
		$this->unofficial = true;
        $this->isd = 2082;

        $this->fighters = array("assault shuttles"=>2);
		
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 4;
        $this->pivotcost = 4;
        $this->iniativebonus = 0;
         
        $this->addPrimarySystem(new Reactor(3, 9, 0, 0));
        $this->addPrimarySystem(new CnC(4, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 2, 4));
        $this->addPrimarySystem(new Engine(3, 7, 0, 6, 4));
		$this->addPrimarySystem(new Magazine(3, 9));
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
      
        $this->addLeftSystem(new Thruster(2, 10, 0, 3, 2));
        $this->addLeftSystem(new Thruster(2, 10, 0, 3, 3));
        $this->addLeftSystem(new Hangar(2, 1));
		$this->addLeftSystem(new NexusHeavySentryGun(2, 7, 3, 300, 60));
		$this->addLeftSystem(new NexusLightAssaultCannon(2, 6, 3, 330, 30));
		$this->addLeftSystem(new NexusCIDS(2, 4, 2, 120, 360));
                
        $this->addRightSystem(new Thruster(2, 10, 0, 3, 2));
        $this->addRightSystem(new Thruster(2, 10, 0, 3, 4));
        $this->addRightSystem(new Hangar(2, 1));
		$this->addRightSystem(new NexusHeavySentryGun(2, 7, 3, 300, 60));
		$this->addRightSystem(new NexusLightAssaultCannon(2, 6, 3, 330, 30));
		$this->addRightSystem(new NexusCIDS(2, 4, 2, 0, 240));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(3, 40));
        $this->addLeftSystem(new Structure(3, 40));
        $this->addRightSystem(new Structure(3, 40));
		
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
					10 => "1:Thruster",
					11 => "Magazine",
                    14 => "Scanner",
                    17 => "Engine",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            3=> array(
					2 => "Connection Strut",
                    6 => "Thruster",
                    8 => "Light Assault Cannon",
                    10 => "Heavy Sentry Gun",
					12 => "Close-In Defense System",
					13 => "Hangar",
					18 => "Structure",
                    20 => "Primary",
            ),
            4=> array(
					2 => "Connection Strut",
                    6 => "Thruster",
                    8 => "Light Assault Cannon",
                    10 => "Heavy Sentry Gun",
					12 => "Close-In Defense System",
					13 => "Hangar",
					18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
