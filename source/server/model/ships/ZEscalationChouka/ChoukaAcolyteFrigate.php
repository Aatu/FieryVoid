<?php
class ChoukaAcolyteFrigate extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 350;
        $this->faction = "ZEscalation Chouka";
        $this->phpclass = "ChoukaAcolyteFrigate";
        $this->imagePath = "img/ships/EscalationWars/ChoukaAcolyte.png";
			$this->canvasSize = 110; //img has 200px per side
        $this->shipClass = "Acolyte Patrol Frigate";
			$this->unofficial = true;
        $this->isd = 1947;
		
        $this->fighters = array("normal"=>6);
        
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.50;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 6*5;
        
        $this->addPrimarySystem(new Reactor(4, 12, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 13, 5, 5));
        $this->addPrimarySystem(new Engine(4, 12, 0, 10, 4));
        $this->addPrimarySystem(new CargoBay(2, 12));
        $this->addPrimarySystem(new CargoBay(2, 12));
        $this->addPrimarySystem(new Thruster(2, 13, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(2, 13, 0, 4, 4));
      
        $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));
		$this->addFrontSystem(new HeavyPlasma(3, 8, 5, 270, 90));
		$this->addFrontSystem(new HeavyPlasma(3, 8, 5, 270, 90));
		$this->addFrontSystem(new LightPlasma(2, 4, 2, 300, 60));
		$this->addFrontSystem(new EWPointPlasmaGun(2, 3, 1, 180, 360));
		$this->addFrontSystem(new EWPointPlasmaGun(2, 3, 1, 0, 180));
                
        $this->addAftSystem(new Thruster(2, 10, 0, 5, 2));
        $this->addAftSystem(new Thruster(2, 10, 0, 5, 2));
		$this->addAftSystem(new Hangar(3, 8));
        $this->addAftSystem(new LightPlasma(2, 4, 2, 120, 240));
        $this->addAftSystem(new LightPlasma(2, 4, 2, 120, 240));
		$this->addAftSystem(new EWHeavyPointPlasmaGun(2, 7, 2, 0, 360));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 30));
        $this->addAftSystem(new Structure( 3, 36));
        $this->addPrimarySystem(new Structure( 4, 30));
		
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
                    11 => "Thruster",
					13 => "Cargo",
					15 => "Scanner",
                    17 => "Engine",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
					7 => "Heavy Plasma Cannon",
                    8 => "Light Plasma Cannon",
					10 => "Point Plasma Gun",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    5 => "Thruster",
					7 => "Heavy Point Plasma Gun",
                    10 => "Light Plasma Cannon",
					12 => "Hangar",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
