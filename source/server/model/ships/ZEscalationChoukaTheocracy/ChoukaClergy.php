<?php
class ChoukaClergy extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 350;
        $this->faction = "ZEscalation Chouka Theocracy";
        $this->phpclass = "ChoukaClergy";
        $this->imagePath = "img/ships/EscalationWars/ChoukaPriest.png";
			$this->canvasSize = 110; //img has 200px per side
        $this->shipClass = "Clergy Laser Destroyer";
			$this->variantOf = "Priest Plasma Destroyer";
			$this->occurence = "rare";
			$this->unofficial = true;
        $this->isd = 1936;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 6*5;
        
        $this->addPrimarySystem(new Reactor(3, 14, 0, 0));
        $this->addPrimarySystem(new CnC(3, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 5, 5));
        $this->addPrimarySystem(new Engine(3, 12, 0, 8, 4));
        $this->addPrimarySystem(new Hangar(2, 2));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 4, 4));
      
        $this->addFrontSystem(new Thruster(2, 6, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 6, 0, 3, 1));
		$this->addFrontSystem(new LightLaser(3, 4, 3, 300, 60));
		$this->addFrontSystem(new LightLaser(3, 4, 3, 300, 60));
		$this->addFrontSystem(new LightLaser(2, 4, 3, 270, 90));
		$this->addFrontSystem(new EWPointPlasmaGun(1, 3, 1, 240, 60));
		$this->addFrontSystem(new EWPointPlasmaGun(1, 3, 1, 300, 120));
                
        $this->addAftSystem(new Thruster(2, 18, 0, 8, 2));
        $this->addAftSystem(new EWPointPlasmaGun(1, 3, 1, 120, 300));
        $this->addAftSystem(new LightLaser(2, 4, 3, 90, 270));
        $this->addAftSystem(new LightLaser(2, 4, 3, 90, 270));
        $this->addAftSystem(new EWPointPlasmaGun(1, 3, 1, 60, 240));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 38));
        $this->addAftSystem(new Structure( 3, 48));
        $this->addPrimarySystem(new Structure( 3, 48));
		
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
                    11 => "Thruster",
					13 => "Scanner",
                    15 => "Engine",
					17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
					7 => "Light Laser",
					10 => "Point Plasma Gun",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
					8 => "Light Laser",
					11 => "Point Plasma Gun",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
