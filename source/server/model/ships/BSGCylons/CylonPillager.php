<?php
class CylonPillager extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 800;
        $this->faction = "BSG Cylons";
        $this->phpclass = "CylonPillager";
        $this->imagePath = "img/ships/BSG/CylonPillager4.png";
        $this->shipClass = "Pillager Long-range Destroyer";
		$this->canvasSize = 165; 
        
        $this->forwardDefense = 13;
        $this->sideDefense = 13;
        
        $this->turncost = 0.50;
        $this->turndelaycost = 0.50;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus = 40;
         
        $this->addPrimarySystem(new Reactor(4, 18, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 14, 5, 8));
        $this->addPrimarySystem(new Engine(4, 18, 0, 12, 3));
        $this->addPrimarySystem(new Hangar(4, 8));
        $this->addPrimarySystem(new Thruster(4, 12, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(4, 12, 0, 5, 4));
       	$this->addPrimarySystem(new LtGuidedMissile(4, 3, 2, 0, 360));
        
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
		$this->addFrontSystem(new MedGuidedMissile(3, 5, 4, 300, 60));
		$this->addFrontSystem(new MedGuidedMissile(3, 5, 4, 300, 60));
		$this->addFrontSystem(new MedGuidedMissileS(3, 5, 4, 300, 60));
		$this->addFrontSystem(new HvyGuidedMissile(3, 7, 6, 300, 60));
        
		$this->addAftSystem(new LtGuidedMissile(3, 3, 2, 120, 240));
		$this->addAftSystem(new LtGuidedMissile(3, 3, 2, 120, 240));
        $this->addAftSystem(new Thruster(3, 14, 0, 6, 2));
        $this->addAftSystem(new Thruster(3, 14, 0, 6, 2));
		$hyperdrive = new JumpEngine(3, 15, 4, 20);
			$hyperdrive->displayName = 'Hyperdrive';
			$this->addAftSystem($hyperdrive);
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 42));
        $this->addAftSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 5, 32));
    
        $this->hitChart = array(
                0=> array(
                    7 => "Structure",
					8 => "Light Guided Missile",
                    10 => "Thruster",
                    13 => "Scanner",
                    16 => "Engine",
                    18 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
                ),
                1=> array(
                    4 => "Thruster",
                    7 => "Heavy Guided Missile",
                    9 => "Special Medium Guided Missile",
                    10 => "Medium Guided Missile",
                    18 => "Structure",
                    20 => "Primary",
                ),
                2=> array(
                    6 => "Thruster",
					8 => "Light Guided Missile",
                    10 => "Jump Engine",
                    18 => "Structure",
                    20 => "Primary",
			),
		);     
        
    }

}



?>