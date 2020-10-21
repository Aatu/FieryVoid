<?php
class ChoukaInquisitorLightCruiser extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 340;
        $this->faction = "ZEscalation Chouka";
        $this->phpclass = "ChoukaInquisitorLightCruiser";
        $this->imagePath = "img/ships/EscalationWars/ChoukaInquisitorLightCruiser.png";
			$this->canvasSize = 130; //img has 200px per side
        $this->shipClass = "Inquisitor Light Cruiser";
			$this->unofficial = true;
        $this->isd = 1921;
        
        $this->fighters = array("normal"=>6);
		
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 6*5;
        
         
        $this->addPrimarySystem(new Reactor(3, 13, 0, 0));
        $this->addPrimarySystem(new CnC(4, 15, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 13, 5, 5));
        $this->addPrimarySystem(new Engine(3, 13, 0, 9, 4));
        $this->addPrimarySystem(new Hangar(3, 8));
        $this->addPrimarySystem(new Thruster(2, 11, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(2, 11, 0, 3, 4));
      
        $this->addFrontSystem(new Thruster(3, 10, 0, 6, 1));
		$this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
		$this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
		$this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
		$this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
		$this->addFrontSystem(new EWPointPlasmaGun(2, 3, 1, 270, 90));
                
        $this->addAftSystem(new Thruster(2, 18, 0, 9, 2));
        $this->addAftSystem(new EWPointPlasmaGun(2, 3, 1, 180, 360));
        $this->addAftSystem(new EWPointPlasmaGun(2, 3, 1, 0, 180));
        $this->addAftSystem(new LightPlasma(1, 4, 2, 120, 240));
        $this->addAftSystem(new LightPlasma(1, 4, 2, 120, 240));

        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 50));
        $this->addAftSystem(new Structure( 3, 50));
        $this->addPrimarySystem(new Structure( 3, 50));
		
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
					8 => "Medium Plasma Cannon",
					9 => "Point Plasma Gun",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
                    8 => "Light Plasma Cannon",
					11 => "Point Plasma Gun",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
