<?php
class Recusant extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 850;
	$this->faction = "Star Wars Clone Wars";
        $this->phpclass = "Recusant";
        $this->imagePath = "img/starwars/CloneWars/Recusant.png";
        $this->shipClass = "Separatist Recusant Light Destroyer";
        $this->shipSizeClass = 3;
		$this->canvasSize = 200; //img has 200px per side
		$this->unofficial = true;
//        $this->limited = 33;

        $this->fighters = array("normal"=>24);

//		$this->isd = 2050;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 17;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 4;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(5, 25, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 17, 5, 7));
        $this->addPrimarySystem(new Engine(5, 18, 0, 9, 5));
		$this->addPrimarySystem(new Hangar(4, 28));
		$this->addPrimarySystem(new JumpEngine(4, 16, 3, 24));

        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
		$this->addFrontSystem(new EMShield(4,6,0,3,240,360)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
        $this->addFrontSystem(new CWTwinHeavyTurbolaser(3, 8, 5, 270, 90));
        $this->addFrontSystem(new CWPointDefenseLaser(2, 4, 1, 240, 60));
        $this->addFrontSystem(new CWPointDefenseLaser(2, 4, 1, 300, 120));
        $this->addFrontSystem(new CWHeavyTurbolaser(4, 6, 6, 240, 360));
        $this->addFrontSystem(new CWHeavyTurbolaser(4, 6, 6, 300, 60));
        $this->addFrontSystem(new CWHeavyTurbolaser(4, 6, 6, 0, 120));
		$this->addFrontSystem(new EMShield(4,6,0,3,0,120)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc

        $this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
        $this->addAftSystem(new CWTwinLaserCannon(2, 4, 2, 180, 360));
        $this->addAftSystem(new CWPointDefenseLaser(2, 4, 1, 120, 300));
        $this->addAftSystem(new CWTwinHeavyTurbolaser(3, 8, 5, 120, 240));
        $this->addAftSystem(new CWPointDefenseLaser(2, 4, 1, 60, 240));
        $this->addAftSystem(new CWTwinLaserCannon(2, 4, 2, 0, 180));

        $this->addLeftSystem(new Thruster(3, 15, 0, 6, 3));
		$this->addLeftSystem(new EMShield(4,6,0,3,180,300)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
		$this->addLeftSystem(new CWTwinTurbolaser(3, 6, 3, 180, 360));
		$this->addLeftSystem(new CWTwinLaserCannon(2, 4, 2, 180, 360));
        $this->addLeftSystem(new CWPointDefenseLaser(2, 4, 1, 180, 360));

        $this->addRightSystem(new Thruster(3, 15, 0, 6, 4));
		$this->addRightSystem(new EMShield(4,6,0,3,60,180)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
		$this->addRightSystem(new CWTwinTurbolaser(3, 6, 3, 0, 180));
		$this->addRightSystem(new CWTwinLaserCannon(2, 4, 2, 0, 180));
        $this->addRightSystem(new CWPointDefenseLaser(2, 4, 1, 0, 180));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(5, 50));
        $this->addAftSystem(new Structure(4, 45));
        $this->addLeftSystem(new Structure(4, 65));
        $this->addRightSystem(new Structure(4, 65));
        $this->addPrimarySystem(new Structure(5, 50));
		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					10 => "Jump Engine",
					14 => "Scanner",
					16 => "Engine",
					18 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					5 => "EM Shield",
					6 => "Point Defense Laser",
					8 => "Heavy Turbolaser",
					10 => "Twin Heavy Turbolaser",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Twin Heavy Turbolaser",
					9 => "Point Defense Laser",
					11 => "Twin Laser Cannon",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					5 => "Thruster",
					6 => "EM Shield",
					8 => "Twin Laser Cannon",
					10 => "Twin Turbolaser",
					11 => "Point Defense Laser",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					6 => "EM Shield",
					8 => "Twin Laser Cannon",
					10 => "Twin Turbolaser",
					11 => "Point Defense Laser",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
