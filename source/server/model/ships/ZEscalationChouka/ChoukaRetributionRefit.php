<?php
class ChoukaRetributionRefit extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 450;
	$this->faction = "ZEscalation Chouka";
        $this->phpclass = "ChoukaRetributionRefit";
        $this->imagePath = "img/ships/EscalationWars/ChoukaRetributionBattlecruiser.png";
        $this->shipClass = "Retribution Battlecruiser Refit";
			$this->variantOf = "Retribution Battlecruiser";
			$this->occurence = "common";
        $this->shipSizeClass = 3;
		$this->canvasSize = 200; //img has 200px per side
		$this->unofficial = true;

	$this->isd = 1962;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 17;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 4;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(3, 18, 0, 0));
        $this->addPrimarySystem(new CnC(3, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 15, 6, 6));
        $this->addPrimarySystem(new Engine(3, 18, 0, 12, 5));
		$this->addPrimarySystem(new Hangar(3, 4));
		$this->addPrimarySystem(new LightPlasma(2, 4, 2, 0, 360));
		
   
        $this->addFrontSystem(new Thruster(3, 16, 0, 6, 1));
		$this->addFrontSystem(new MediumPlasma(3, 5, 3, 330, 30));
		$this->addFrontSystem(new LightLaser(1, 4, 3, 300, 60));
		$this->addFrontSystem(new LightLaser(1, 4, 3, 300, 60));
		$this->addFrontSystem(new LightLaser(1, 4, 3, 300, 60));
		$this->addFrontSystem(new LightLaser(1, 4, 3, 300, 60));


        $this->addAftSystem(new Thruster(2, 7, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 7, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 6, 2));
		$this->addAftSystem(new EWTwinLaserCannon(2, 8, 4, 120, 240));
		$this->addAftSystem(new LightLaser(1, 4, 3, 180, 300));
		$this->addAftSystem(new LightLaser(1, 4, 3, 60, 180));


        $this->addLeftSystem(new EWTwinLaserCannon(2, 8, 4, 270, 90));
        $this->addLeftSystem(new Thruster(2, 13, 0, 6, 3));


        $this->addRightSystem(new EWTwinLaserCannon(2, 8, 4, 270, 90));
        $this->addRightSystem(new Thruster(2, 13, 0, 6, 4));


        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(3, 44));
        $this->addAftSystem(new Structure(3, 40));
        $this->addLeftSystem(new Structure(3, 36));
        $this->addRightSystem(new Structure(3, 36));
        $this->addPrimarySystem(new Structure(3, 40));
		
		$this->hitChart = array(
			0=> array(
					10 => "Structure",
					11 => "Light Plasma Cannon",
					13 => "Scanner",
					15 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					6 => "Medium Plasma Cannon",
					9 => "Light Laser",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					9 => "Twin Laser Cannon",
					11 => "Light Laser",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					7 => "Twin Laser Cannon",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					7 => "Twin Laser Cannon",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
