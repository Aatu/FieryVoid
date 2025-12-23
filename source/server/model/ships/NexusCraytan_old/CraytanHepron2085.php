<?php
class CraytanHepron2085 extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 380;
        $this->faction = "Nexus Craytan Union (early)";
        $this->phpclass = "CraytanHepron2085";
        $this->imagePath = "img/ships/Nexus/craytan_hepron.png";
		$this->canvasSize = 140; //img has 200px per side
        $this->shipClass = "Hepron Cruiser (2085)";
			$this->variantOf = "Hepron Early Cruiser";
			$this->occurence = "common";
		$this->unofficial = true;
        $this->isd = 2085;

        $this->fighters = array("assault shuttles"=>4);
		
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 6*5;
         
        $this->addPrimarySystem(new Reactor(3, 19, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 4, 6));
        $this->addPrimarySystem(new Engine(3, 14, 0, 8, 4));
        $this->addPrimarySystem(new Hangar(2, 4));
		$this->addPrimarySystem(new Magazine(3, 9));
        $this->addPrimarySystem(new Thruster(2, 13, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(2, 13, 0, 5, 4));
      
        $this->addFrontSystem(new Thruster(2, 15, 0, 6, 1));
		$this->addFrontSystem(new NexusCIDS(2, 6, 2, 180, 60));
		$this->addFrontSystem(new MediumPlasma(2, 5, 3, 240, 60));
		$this->addFrontSystem(new HeavyPlasma(2, 8, 5, 300, 360));
		$this->addFrontSystem(new HeavyPlasma(2, 8, 5, 0, 60));
		$this->addFrontSystem(new MediumPlasma(2, 5, 3, 300, 120));
		$this->addFrontSystem(new NexusCIDS(2, 6, 2, 300, 180));
                
        $this->addAftSystem(new Thruster(2, 13, 0, 4, 2));
        $this->addAftSystem(new Thruster(2, 13, 0, 4, 2));
		$this->addAftSystem(new NexusCIDS(2, 4, 2, 120, 360));
		$this->addAftSystem(new NexusLightSentryGun(2, 5, 1, 90, 270));
		$this->addAftSystem(new NexusMedSentryGun(2, 6, 2, 120, 240));
		$this->addAftSystem(new NexusLightSentryGun(2, 5, 1, 90, 270));
		$this->addAftSystem(new NexusCIDS(2, 4, 2, 0, 240));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 33));
        $this->addAftSystem(new Structure( 3, 33));
        $this->addPrimarySystem(new Structure( 4, 36));
		
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
					10 => "Magazine",
                    12 => "Thruster",
                    14 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
                    6 => "Heavy Plasma Cannon",
                    8 => "Medium Plasma Cannon",
					10 => "Advanced Close-In Defense System",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
                    8 => "Close-In Defense System",
					10 => "Light Sentry Gun",
					12 => "Medium Sentry Gun",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
