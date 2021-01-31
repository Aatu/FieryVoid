<?php
class ChoukaHeraldWarCruiser extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 800;
	$this->faction = "ZEscalation Chouka Theocracy";
        $this->phpclass = "ChoukaHeraldWarCruiser";
        $this->imagePath = "img/ships/EscalationWars/ChoukaApostle.png";
        $this->shipClass = "Herald War Cruiser";
			$this->variantOf = "Apostle Holy Cruiser";
			$this->occurence = "rare";		
			$this->limited = 33;
        $this->shipSizeClass = 3;
		$this->canvasSize = 200; //img has 200px per side
		$this->unofficial = true;

        $this->fighters = array("normal"=>12);

		$this->isd = 1972;
        
        $this->forwardDefense = 16;
        $this->sideDefense = 18;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.33;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 5;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(4, 34, 0, 0));
        $this->addPrimarySystem(new CnC(4, 25, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 20, 7, 7));
        $this->addPrimarySystem(new Engine(4, 16, 0, 8, 5));
		$this->addPrimarySystem(new Hangar(3, 15));
		$this->addPrimarySystem(new JumpEngine(4, 15, 4, 36));
		$this->addPrimarySystem(new SoMissileRack(3, 6, 0, 0, 360));
		$this->addPrimarySystem(new SoMissileRack(3, 6, 0, 0, 360));
		
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
		$this->addFrontSystem(new EWTwinLaserCannon(2, 8, 5, 300, 60));
		$this->addFrontSystem(new EWTwinLaserCannon(2, 8, 5, 300, 60));
		$this->addFrontSystem(new EWHeavyPointPlasmaGun(2, 7, 2, 240, 60));
		$this->addFrontSystem(new EWHeavyPointPlasmaGun(2, 7, 2, 300, 120));
		$this->addFrontSystem(new EWHeavyPointPlasmaGun(2, 7, 2, 270, 90));

        $this->addAftSystem(new Thruster(3, 24, 0, 8, 2));
		$this->addAftSystem(new EWHeavyPointPlasmaGun(2, 7, 2, 180, 360));
		$this->addAftSystem(new EWHeavyPointPlasmaGun(2, 7, 2, 180, 360));
		$this->addAftSystem(new EWHeavyPointPlasmaGun(2, 7, 2, 0, 180));
		$this->addAftSystem(new EWHeavyPointPlasmaGun(2, 7, 2, 0, 180));

		$this->addLeftSystem(new MediumPlasma(2, 5, 3, 180, 300));
		$this->addLeftSystem(new MediumPlasma(2, 5, 3, 180, 300));
        $this->addLeftSystem(new HeavyPlasma(3, 8, 5, 240, 360));
        $this->addLeftSystem(new HeavyPlasma(3, 8, 5, 240, 360));
        $this->addLeftSystem(new Thruster(3, 15, 0, 4, 3));

		$this->addRightSystem(new MediumPlasma(2, 5, 3, 60, 180));
		$this->addRightSystem(new MediumPlasma(2, 5, 3, 60, 180));
        $this->addRightSystem(new HeavyPlasma(3, 8, 5, 0, 120));
        $this->addRightSystem(new HeavyPlasma(3, 8, 5, 0, 120));
        $this->addRightSystem(new Thruster(3, 15, 0, 4, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 70));
        $this->addAftSystem(new Structure(4, 56));
        $this->addLeftSystem(new Structure(4, 70));
        $this->addRightSystem(new Structure(4, 70));
        $this->addPrimarySystem(new Structure(4, 78));
		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					10 => "Jump Engine",
					12 => "Class-SO Missile Rack",
					14 => "Scanner",
					16 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					3 => "Thruster",
					6 => "Twin Laser Cannon",
					10 => "Heavy Point Plasma Gun",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					9 => "Heavy Point Plasma Gun",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					7 => "Heavy Plasma Cannon",
					11 => "Medium Plasma Cannon",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					7 => "Heavy Plasma Cannon",
					11 => "Medium Plasma Cannon",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
