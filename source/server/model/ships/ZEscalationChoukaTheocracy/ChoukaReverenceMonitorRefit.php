<?php
class ChoukaReverenceMonitorRefit extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 600;
	$this->faction = "ZEscalation Chouka Theocracy";
        $this->phpclass = "ChoukaReverenceMonitorRefit";
        $this->imagePath = "img/ships/EscalationWars/ChoukaReverence.png";
        $this->shipClass = "Reverence System Monitor (1948 Refit)";
			$this->variantOf = "Reverence System Monitor";
			$this->occurence = "common";		
        $this->shipSizeClass = 3;
		$this->canvasSize = 160; //img has 200px per side
		$this->unofficial = true;
        $this->limited = 33;

        $this->fighters = array("normal"=>24);

		$this->isd = 1948;
        
        $this->forwardDefense = 17;
        $this->sideDefense = 17;
        
        $this->turncost = 1.5;
        $this->turndelaycost = 1.5;
        $this->accelcost = 4;
        $this->rollcost = 4;
        $this->pivotcost = 3;
        $this->iniativebonus = -10;
        
        $this->addPrimarySystem(new Reactor(4, 25, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 5, 6));
        $this->addPrimarySystem(new Engine(4, 12, 0, 8, 5));
		$this->addPrimarySystem(new Hangar(3, 28));
        $this->addPrimarySystem(new ReloadRack(4, 9));
		
        $this->addFrontSystem(new Thruster(3, 13, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 13, 0, 4, 1));
		$this->addFrontSystem(new LightPlasma(2, 4, 2, 270, 90));
		$this->addFrontSystem(new LightPlasma(2, 4, 2, 270, 90));
		$this->addFrontSystem(new MediumPlasma(3, 5, 3, 270, 90));
		$this->addFrontSystem(new MediumPlasma(3, 5, 3, 270, 90));
		$this->addFrontSystem(new LightLaser(1, 4, 3, 240, 120));
		$this->addFrontSystem(new LightLaser(1, 4, 3, 240, 120));

        $this->addAftSystem(new Thruster(2, 12, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 12, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 18, 0, 4, 2));
		$this->addAftSystem(new LightPlasma(2, 4, 2, 90, 270));
		$this->addAftSystem(new LightPlasma(2, 4, 2, 90, 270));
		$this->addAftSystem(new MediumPlasma(3, 5, 3, 120, 240));
		$this->addAftSystem(new MediumPlasma(3, 5, 3, 120, 240));

        $this->addLeftSystem(new MediumPlasma(3, 5, 3, 240, 360));
        $this->addLeftSystem(new MediumPlasma(3, 5, 3, 240, 360));
		$this->addLeftSystem(new SoMissileRack(3, 6, 0, 180, 60));
		$this->addLeftSystem(new SoMissileRack(3, 6, 0, 180, 60));
        $this->addLeftSystem(new EWPointPlasmaGun(1, 3, 1, 180, 360));
        $this->addLeftSystem(new EWPointPlasmaGun(1, 3, 1, 180, 360));
        $this->addLeftSystem(new EWPointPlasmaGun(1, 3, 1, 180, 360));
        $this->addLeftSystem(new Thruster(3, 15, 0, 4, 3));

        $this->addRightSystem(new MediumPlasma(3, 5, 3, 0, 120));
        $this->addRightSystem(new MediumPlasma(3, 5, 3, 0, 120));
		$this->addRightSystem(new SoMissileRack(3, 6, 0, 300, 180));
		$this->addRightSystem(new SoMissileRack(3, 6, 0, 300, 180));
        $this->addRightSystem(new EWPointPlasmaGun(1, 3, 1, 0, 180));
        $this->addRightSystem(new EWPointPlasmaGun(1, 3, 1, 0, 180));
        $this->addRightSystem(new EWPointPlasmaGun(1, 3, 1, 0, 180));
        $this->addRightSystem(new Thruster(3, 15, 0, 4, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 40));
        $this->addAftSystem(new Structure(4, 40));
        $this->addLeftSystem(new Structure(4, 63));
        $this->addRightSystem(new Structure(4, 63));
        $this->addPrimarySystem(new Structure(5, 66));
		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					9 => "Reload Rack",
					12 => "Scanner",
					14 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					7 => "Medium Plasma Cannon",
					9 => "Light Plasma Cannon",
					11 => "Light Laser",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Medium Plasma Cannon",
					10 => "Light Plasma Cannon",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					3 => "Thruster",
					6 => "Medium Plasma Cannon",
					9 => "Class-SO Missile Rack",
					12 => "Point Plasma Gun",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					3 => "Thruster",
					6 => "Medium Plasma Cannon",
					9 => "Class-SO Missile Rack",
					12 => "Point Plasma Gun",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
