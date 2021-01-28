<?php
class CircasianCairn extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 300;
        $this->faction = "ZEscalation Circasian";
        $this->phpclass = "CircasianCairn";
        $this->imagePath = "img/ships/EscalationWars/CircasianCairn.png";
			$this->canvasSize = 125; //img has 200px per side
        $this->shipClass = "Cairn Carrier";
			$this->unofficial = true;
        $this->isd = 1933;
		$this->fighters = array("light"=>18, "heavy"=>6);
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 6*5;
        
         
        $this->addPrimarySystem(new Reactor(3, 10, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 2, 4));
        $this->addPrimarySystem(new Engine(3, 9, 0, 8, 2));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 3, 4));
      
        $this->addFrontSystem(new Thruster(2, 8, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 8, 0, 2, 1));
        $this->addFrontSystem(new Hangar(3, 26));
		$this->addFrontSystem(new EWRocketLauncher(1, 4, 1, 240, 360));
		$this->addFrontSystem(new EWRocketLauncher(1, 4, 1, 240, 360));
		$this->addFrontSystem(new EWRocketLauncher(1, 4, 1, 0, 120));
		$this->addFrontSystem(new EWRocketLauncher(1, 4, 1, 0, 120));
                
        $this->addAftSystem(new Thruster(2, 15, 0, 8, 2));
        $this->addAftSystem(new LightLaser(1, 4, 3, 180, 360));
        $this->addAftSystem(new LightLaser(1, 4, 3, 180, 360));
        $this->addAftSystem(new LightLaser(1, 4, 3, 0, 180));
        $this->addAftSystem(new LightLaser(1, 4, 3, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 36));
        $this->addAftSystem(new Structure( 3, 36));
        $this->addPrimarySystem(new Structure( 3, 36));
		
        $this->hitChart = array(
            0=> array(
                    10 => "Structure",
                    13 => "Thruster",
                    15 => "Scanner",
                    17 => "Engine",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
                    7 => "Rocket Launcher",
					10 => "Hangar",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
                    9 => "Light Laser",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
