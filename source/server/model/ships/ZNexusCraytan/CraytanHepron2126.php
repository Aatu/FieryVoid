<?php
class CraytanHepron2126 extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 380;
        $this->faction = "ZNexus Playtest Craytan";
        $this->phpclass = "CraytanHepron2126";
        $this->imagePath = "img/ships/Nexus/CraytanHepron.png";
		$this->canvasSize = 140; //img has 200px per side
        $this->shipClass = "Hepron Early Cruiser (2126 refit)";
			$this->variantOf = "Hepron Early Cruiser";
			$this->occurence = "common";
		$this->unofficial = true;
        $this->isd = 2126;
		
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 6*5;
         
        $this->addPrimarySystem(new Reactor(3, 16, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 4, 6));
        $this->addPrimarySystem(new Engine(3, 14, 0, 8, 4));
        $this->addPrimarySystem(new Hangar(2, 4));
		$this->addPrimarySystem(new CargoBay(3, 9));
        $this->addPrimarySystem(new Thruster(2, 13, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(2, 13, 0, 5, 4));
      
        $this->addFrontSystem(new Thruster(2, 15, 0, 6, 1));
		$this->addFrontSystem(new NexusACIDS(2, 6, 2, 180, 60));
		$this->addFrontSystem(new MediumPlasma(2, 5, 3, 240, 60));
		$this->addFrontSystem(new NexusLightAssaultCannon(2, 6, 3, 300, 360));
		$this->addFrontSystem(new NexusLightAssaultCannon(2, 6, 3, 0, 60));
		$this->addFrontSystem(new MediumPlasma(2, 5, 3, 300, 120));
		$this->addFrontSystem(new NexusACIDS(2, 6, 2, 300, 180));
                
        $this->addAftSystem(new Thruster(2, 13, 0, 4, 2));
        $this->addAftSystem(new Thruster(2, 13, 0, 4, 2));
		$this->addAftSystem(new NexusACIDS(2, 6, 2, 120, 360));
		$this->addAftSystem(new LightPlasma(2, 4, 2, 90, 270));
		$this->addAftSystem(new LightPlasma(2, 4, 2, 90, 270));
		$this->addAftSystem(new NexusACIDS(2, 6, 2, 0, 240));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 33));
        $this->addAftSystem(new Structure( 3, 33));
        $this->addPrimarySystem(new Structure( 4, 36));
		
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
					10 => "Cargo Bay",
                    12 => "Thruster",
                    14 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    3 => "Thruster",
                    6 => "Light Assault Cannon",
                    8 => "Medium Plasma Cannon",
					10 => "Advanced Close-In Defense System",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
                    8 => "Advanced Close-In Defense System",
					10 => "Light Plasma Cannon",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
