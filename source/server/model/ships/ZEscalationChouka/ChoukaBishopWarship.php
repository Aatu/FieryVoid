<?php
class ChoukaBishopWarship extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 450;
        $this->faction = "ZEscalation Chouka";
        $this->phpclass = "ChoukaBishopWarship";
        $this->imagePath = "img/ships/EscalationWars/ChoukaBishopWarship.png";
			$this->canvasSize = 110; //img has 200px per side
        $this->shipClass = "Bishop Warship";
			$this->unofficial = true;
        $this->isd = 1964;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 0.50;
        $this->turndelaycost = 0.50;
        $this->accelcost = 2;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        $this->iniativebonus = 6*5;
        
         
        $this->addPrimarySystem(new Reactor(4, 15, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 15, 6, 6));
        $this->addPrimarySystem(new Engine(4, 12, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new Quarters(5, 9));
        $this->addPrimarySystem(new Thruster(3, 11, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(3, 11, 0, 5, 4));
      
        $this->addFrontSystem(new Thruster(2, 8, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 8, 0, 2, 1));
		$this->addFrontSystem(new EWTwinLaserCannon(3, 8, 4, 300, 60));
		$this->addFrontSystem(new MediumPlasma(2, 5, 3, 240, 360));
		$this->addFrontSystem(new MediumPlasma(2, 5, 3, 0, 120));
		$this->addFrontSystem(new LightPlasma(2, 4, 2, 270, 90));
		$this->addFrontSystem(new LightPlasma(2, 4, 2, 270, 90));
                
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new MediumPlasma(2, 5, 3, 180, 240));
        $this->addAftSystem(new MediumPlasma(2, 5, 3, 120, 180));

        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 52));
        $this->addAftSystem(new Structure( 3, 48));
        $this->addPrimarySystem(new Structure( 4, 44));
		
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
                    10 => "Thruster",
					12 => "Scanner",
                    14 => "Engine",
					16 => "Hangar",
                    18 => "Reactor",
					19 => "Quarters",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
					6 => "Twin Laser Cannon",
                    8 => "Medium Plasma Cannon",
					11 => "Light Plasma Cannon",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    8 => "Thruster",
                    10 => "Medium Plasma Cannon",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
