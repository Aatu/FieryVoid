<?php
class CraytanToprenEarly extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 365;
        $this->faction = "ZNexus Craytan Union";
        $this->phpclass = "CraytanToprenEarly";
        $this->imagePath = "img/ships/Nexus/craytan_topren.png";
		$this->canvasSize = 125; //img has 200px per side
        $this->shipClass = "Topren Prototype (2110)";
			$this->variantOf = "Topren Patrol Destroyer";
			$this->occurence = "common";
		$this->unofficial = true;
        $this->isd = 2110;

        $this->fighters = array("assault shuttles"=>3);
		
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 6*5;
         
        $this->addPrimarySystem(new Reactor(5, 16, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 12, 4, 6));
        $this->addPrimarySystem(new Engine(4, 14, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(3, 3));
		$this->addPrimarySystem(new Magazine(4, 9));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 5, 4));
      
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
		$this->addFrontSystem(new NexusLightSentryGun(2, 5, 1, 240, 60));
		$this->addFrontSystem(new HeavyPlasma(3, 8, 5, 240, 360));
		$this->addFrontSystem(new NexusMedAssaultCannon(4, 7, 4, 330, 30));
		$this->addFrontSystem(new HeavyPlasma(3, 8, 5, 0, 120));
		$this->addFrontSystem(new NexusLightSentryGun(2, 5, 1, 300, 120));
                
        $this->addAftSystem(new Thruster(3, 13, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 13, 0, 4, 2));
		$this->addAftSystem(new NexusACIDS(2, 6, 2, 120, 360));
		$this->addAftSystem(new NexusLightSentryGun(2, 5, 1, 120, 300));
		$this->addAftSystem(new NexusLightSentryGun(2, 5, 1, 60, 240));
		$this->addAftSystem(new NexusACIDS(2, 6, 2, 0, 240));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 40));
        $this->addAftSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 5, 40));
		
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
					9 => "Magazine",
                    12 => "Thruster",
                    14 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
                    6 => "Medium Assault Cannon",
                    8 => "Heavy Plasma Cannon",
					10 => "Light Sentry Gun",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
                    8 => "Light Sentry Gun",
					10 => "Advanced Close-In Defense System",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
