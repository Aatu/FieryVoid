<?php
class ChoukaRetributionRefit extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 600;
	$this->faction = "ZEscalation Chouka Theocracy";
        $this->phpclass = "ChoukaRetributionRefit";
        $this->imagePath = "img/ships/EscalationWars/ChoukaRetribution.png";
        $this->shipClass = "Retribution Battlecruiser (1962 Refit)";
			$this->variantOf = "Retribution Battlecruiser";
			$this->occurence = "common";			
        $this->limited = 33;
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
        
        $this->addPrimarySystem(new Reactor(4, 18, 0, 0));
        $this->addPrimarySystem(new CnC(4, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 15, 6, 6));
        $this->addPrimarySystem(new Engine(4, 18, 0, 12, 5));
		$this->addPrimarySystem(new Hangar(3, 4));
		
        $this->addFrontSystem(new Thruster(3, 16, 0, 6, 1));
		$this->addFrontSystem(new EWHeavyPointPlasmaGun(2, 7, 2, 240, 120));
		$this->addFrontSystem(new EWTwinLaserCannon(2, 8, 5, 300, 60));
		$this->addFrontSystem(new EWTwinLaserCannon(2, 8, 5, 300, 60));
		$this->addFrontSystem(new LightLaser(2, 4, 3, 240, 360));
		$this->addFrontSystem(new LightLaser(2, 4, 3, 0, 120));

        $this->addAftSystem(new Thruster(2, 7, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 7, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 18, 0, 6, 2));
		$this->addAftSystem(new EWTwinLaserCannon(2, 8, 5, 120, 240));
		$this->addAftSystem(new EWTwinLaserCannon(2, 8, 5, 120, 240));
		$this->addAftSystem(new LightLaser(2, 4, 3, 180, 300));
		$this->addAftSystem(new LightLaser(2, 4, 3, 60, 180));
		$this->addAftSystem(new JumpEngine(3, 16, 4, 40));

        $this->addLeftSystem(new MediumPlasma(2, 5, 3, 240, 360));
		$this->addLeftSystem(new EWPointPlasmaGun(2, 3, 1, 180, 360));
		$this->addLeftSystem(new EWPointPlasmaGun(2, 3, 1, 180, 360));
        $this->addLeftSystem(new Thruster(3, 13, 0, 6, 3));

        $this->addRightSystem(new MediumPlasma(2, 5, 3, 0, 120));
		$this->addRightSystem(new EWPointPlasmaGun(2, 3, 1, 0, 180));
		$this->addRightSystem(new EWPointPlasmaGun(2, 3, 1, 0, 180));
        $this->addRightSystem(new Thruster(3, 13, 0, 6, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 44));
        $this->addAftSystem(new Structure(3, 44));
        $this->addLeftSystem(new Structure(3, 36));
        $this->addRightSystem(new Structure(3, 36));
        $this->addPrimarySystem(new Structure(4, 40));
		
		$this->hitChart = array(
			0=> array(
					10 => "Structure",
					12 => "Scanner",
					15 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					7 => "Heavy Plasma Cannon",
					9 => "Medium Plasma Cannon",
					11 => "Light Laser",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Jump Engine",
					10 => "Heavy Plasma Cannon",
					12 => "Light Laser",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					5 => "Thruster",
					7 => "Medium Plasma Cannon",
					10 => "Point Plasma Gun",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					7 => "Medium Plasma Cannon",
					10 => "Point Plasma Gun",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
