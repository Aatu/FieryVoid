<?php
class Darius_WI extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 500;
        $this->faction = "ZWhatIF";
        $this->phpclass = "Darius_WI";
        $this->imagePath = "img/ships/darkner.png";
        $this->shipClass = "Centauri Darius Fast Attack Frigate";
//			$this->variantOf = "Darkner Fast Attack Frigate";
//			$this->occurence = "uncommon";
		$this->unofficial = true;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 13;
        
        $this->turncost = 0.50;
        $this->turndelaycost = 0.50;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus = 40;
         
        $this->addPrimarySystem(new Reactor(5, 17, 0, 4));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 14, 5, 9));
        $this->addPrimarySystem(new Engine(5, 18, 0, 12, 3));
        $this->addPrimarySystem(new Hangar(4, 1));
        $this->addPrimarySystem(new Thruster(4, 10, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(4, 10, 0, 5, 4));
        
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new BattleLaser(4, 6, 6, 240, 0));
        $this->addFrontSystem(new BattleLaser(4, 6, 6, 0, 120));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 180, 0));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 0, 180));
        
        $this->addAftSystem(new Thruster(4, 14, 0, 6, 2));
        $this->addAftSystem(new Thruster(4, 14, 0, 6, 2));
        $this->addAftSystem(new JumpEngine(4, 15, 4, 20));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 42));
        $this->addAftSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 5, 32));
    
        $this->hitChart = array(
                0=> array(
                    7 => "Structure",
                    10 => "Thruster",
                    13 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
                ),
                1=> array(
                    4 => "Thruster",
                    7 => "Battle Laser",
                    9 => "Twin Array",
                    18 => "Structure",
                    20 => "Primary",
                ),
                2=> array(
                    6 => "Thruster",
                    10 => "Jump Engine",
                    18 => "Structure",
                    20 => "Primary",
			),
		);     
        
    }

}



?>
