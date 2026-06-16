<?php
class ChoukaCrusaderDreadnought extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 850;
	$this->faction = "Escalation Wars Chouka Theocracy";
        $this->phpclass = "ChoukaCrusaderDreadnought";
        $this->imagePath = "img/ships/EscalationWars/ChoukaCrusader.png";
        $this->shipClass = "Crusader Dreadnought";
        $this->shipSizeClass = 3;
		$this->canvasSize = 235; //img has 200px per side
		$this->unofficial = true;
        $this->occurence = "unique"; 

		$this->isd = 1972;
        
        $this->forwardDefense = 16;
        $this->sideDefense = 19;
        
        $this->turncost = 1.5;
        $this->turndelaycost = 1.5;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 4;
        $this->iniativebonus = 0;

		$cnc = new CnC(5, 25, 0, 0);
		$cnc->startArc = 0;
		$cnc->endArc = 360;
        $this->addPrimarySystem($cnc);
		$cnc = new SecondaryCnC(6, 9, 0, 0); //all-around by default
        $this->addPrimarySystem($cnc);
        
        $this->addPrimarySystem(new Reactor(5, 35, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 24, 8, 8));
        $this->addPrimarySystem(new Engine(5, 18, 0, 12, 6));
		$crusaderHangar = new Hangar(4, 8, 2);
		$crusaderHangar->directions = array(1, 2, 4, 5); //port + starboard launch bays — player picks per launch
		$this->addPrimarySystem($crusaderHangar);
//		$this->addPrimarySystem(new Hangar(4, 8, 2));
		$this->addPrimarySystem(new JumpEngine(5, 15, 4, 32));
		
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
		$this->addFrontSystem(new EWTwinLaserCannon(3, 8, 5, 300, 60));
		$this->addFrontSystem(new EWTwinLaserCannon(3, 8, 5, 300, 60));
		$this->addFrontSystem(new EWHeavyPointPlasmaGun(2, 7, 3, 270, 90));
		$this->addFrontSystem(new EWHeavyPointPlasmaGun(2, 7, 3, 270, 90));

        $this->addAftSystem(new Thruster(2, 10, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 24, 0, 8, 2));
        $this->addAftSystem(new Thruster(2, 10, 0, 2, 2));
		$this->addAftSystem(new EWTwinLaserCannon(3, 8, 5, 180, 300));
		$this->addAftSystem(new EWTwinLaserCannon(3, 8, 5, 120, 240));
		$this->addAftSystem(new EWTwinLaserCannon(3, 8, 5, 60, 180));
		$this->addAftSystem(new EWHeavyPointPlasmaGun(2, 7, 3, 90, 270));

        $this->addLeftSystem(new Thruster(3, 15, 0, 4, 3));
		$this->addLeftSystem(new EWTwinLaserCannon(3, 8, 5, 240, 360));
		$this->addLeftSystem(new EWTwinLaserCannon(3, 8, 5, 240, 360));
		$this->addLeftSystem(new EWHeavyPointPlasmaGun(2, 7, 3, 180, 360));
		$this->addLeftSystem(new LightLaser(2, 4, 3, 180, 360));
		$this->addLeftSystem(new LightLaser(2, 4, 3, 180, 360));

        $this->addRightSystem(new Thruster(3, 15, 0, 4, 4));
		$this->addRightSystem(new EWTwinLaserCannon(3, 8, 5, 0, 120));
		$this->addRightSystem(new EWTwinLaserCannon(3, 8, 5, 0, 120));
		$this->addRightSystem(new EWHeavyPointPlasmaGun(2, 7, 3, 0, 180));
		$this->addRightSystem(new LightLaser(2, 4, 3, 0, 180));
		$this->addRightSystem(new LightLaser(2, 4, 3, 0, 180));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 80));
        $this->addAftSystem(new Structure(4, 63));
        $this->addLeftSystem(new Structure(4, 90));
        $this->addRightSystem(new Structure(4, 90));
        $this->addPrimarySystem(new Structure(5, 96));
		
		$this->hitChart = array(
			0=> array(
					9 => "Structure",
					11 => "Jump Engine",
					13 => "Scanner",
					15 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "TAG:C&C",
			),
			1=> array(
					3 => "Thruster",
					6 => "Twin Laser Cannon",
					8 => "Heavy Point Plasma Gun",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					9 => "Twin Laser Cannon",
					11 => "Heavy Point Plasma Gun",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					3 => "Thruster",
					6 => "Twin Laser Cannon",
					8 => "Heavy Point Plasma Gun",
					10 => "Light Laser",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					3 => "Thruster",
					6 => "Twin Laser Cannon",
					8 => "Heavy Point Plasma Gun",
					10 => "Light Laser",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
