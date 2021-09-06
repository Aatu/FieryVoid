<?php
class CivilianHastal extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 185;
        $this->faction = "ZEscalation Civilian";
        $this->phpclass = "CivilianHastal";
        $this->imagePath = "img/ships/EscalationWars/CivilianHastal.png";
			$this->canvasSize = 125; //img has 200px per side
        $this->shipClass = "Hastal Jump Transport";
			$this->unofficial = true;
        $this->isd = 1960;

		$this->limited = 10;
 		$this->notes = 'Circasian Empire';
       
        $this->forwardDefense = 13;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 6;
        $this->iniativebonus = -10;

		$cA = new CargoBay(2, 30);
		$cB = new CargoBay(2, 30);

		$cA->displayName = "Cargo Bay A";
		$cB->displayName = "Cargo Bay B";
         
        $this->addPrimarySystem(new Reactor(3, 13, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(2, 12, 3, 4));
        $this->addPrimarySystem(new Engine(3, 13, 0, 10, 4));
        $this->addPrimarySystem(new Hangar(3, 6));
        $this->addPrimarySystem(new Thruster(2, 13, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(2, 13, 0, 4, 4));
      
        $this->addFrontSystem(new Thruster(2, 10, 0, 3, 1));
		$this->addFrontSystem(new LightRailGun(2, 6, 3, 180, 360));
		$this->addFrontSystem(new NexusUltralightRailgun(1, 3, 2, 240, 120));
		$this->addFrontSystem(new LightRailGun(2, 6, 3, 0, 180));
        $this->addFrontSystem($cA);
        $this->addFrontSystem($cB);
                
        $this->addAftSystem(new Thruster(2, 12, 0, 5, 2));
		$this->addAftSystem(new NexusUltralightRailgun(1, 3, 2, 120, 360));
        $this->addAftSystem(new JumpEngine(2, 10, 4, 40));
        $this->addAftSystem(new NexusUltralightRailgun(1, 3, 2, 0, 240));
        $this->addAftSystem(new Thruster(2, 12, 0, 5, 2));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 32));
        $this->addAftSystem(new Structure( 3, 36));
        $this->addPrimarySystem(new Structure( 3, 36));
		
        $this->hitChart = array(
            0=> array(
                    6 => "Structure",
                    9 => "Thruster",
                    12 => "Scanner",
                    14 => "Hangar",
                    16 => "Engine",
                    18 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
                    6 => "Light Railgun",
					7 => "Ultralight Railgun",
					9 => "Cargo Bay A",
					11 => "Cargo Bay B",
					17 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
                    8 => "Ultralight Railgun",
					11 => "Jump Engine",
                    17 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
