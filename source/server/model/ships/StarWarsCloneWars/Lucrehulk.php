<?php
class Lucrehulk extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 1000;
	$this->faction = "Star Wars Clone Wars";
        $this->phpclass = "Lucrehulk";
        $this->imagePath = "img/starwars/CloneWars/Lucrehulk.png";
        $this->shipClass = "Separatist Lucrehulk Battleship";
        $this->shipSizeClass = 3;
		$this->canvasSize = 200; //img has 200px per side
		$this->unofficial = true;
//        $this->limited = 33;

        $this->fighters = array("normal"=>96);

//		$this->isd = 2050;
        
        $this->forwardDefense = 18;
        $this->sideDefense = 18;
        
        $this->turncost = 1.5;
        $this->turndelaycost = 1.5;
        $this->accelcost = 4;
        $this->rollcost = 4;
        $this->pivotcost = 4;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(5, 30, 0, 0));
        $this->addPrimarySystem(new CnC(6, 25, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 20, 5, 8));
        $this->addPrimarySystem(new Engine(5, 20, 0, 9, 5));
		$this->addPrimarySystem(new Hangar(4, 52));
		$this->addPrimarySystem(new JumpEngine(4, 20, 3, 30));
        $this->addPrimarySystem(new CWPointDefenseLaser(2, 4, 1, 0, 360));
        $this->addPrimarySystem(new CWPointDefenseLaser(2, 4, 1, 0, 360));

        $this->addFrontSystem(new Thruster(3, 12, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 12, 0, 4, 1));
		$this->addFrontSystem(new EMShield(3,6,0,2,300,360)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
        $this->addFrontSystem(new CWQuadTurbolaser(3, 8, 4, 240, 360));
        $this->addFrontSystem(new CWPointDefenseLaser(2, 4, 1, 270, 90));
        $this->addFrontSystem(new CWPointDefenseLaser(2, 4, 1, 270, 90));
        $this->addFrontSystem(new CWPointDefenseLaser(2, 4, 1, 270, 90));
        $this->addFrontSystem(new CWPointDefenseLaser(2, 4, 1, 270, 90));
        $this->addFrontSystem(new CWQuadTurbolaser(3, 8, 4, 0, 120));
		$this->addFrontSystem(new EMShield(3,6,0,2,0,60)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc

        $this->addAftSystem(new Thruster(3, 15, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 3, 2));
		$this->addAftSystem(new EMShield(3,6,0,2,180,240)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
        $this->addAftSystem(new CWQuadTurbolaser(3, 8, 4, 180, 300));
        $this->addAftSystem(new CWPointDefenseLaser(2, 4, 1, 90, 270));
        $this->addAftSystem(new CWPointDefenseLaser(2, 4, 1, 90, 270));
        $this->addAftSystem(new CWPointDefenseLaser(2, 4, 1, 90, 270));
        $this->addAftSystem(new CWPointDefenseLaser(2, 4, 1, 90, 270));
        $this->addAftSystem(new CWQuadTurbolaser(3, 8, 4, 60, 180));
		$this->addAftSystem(new EMShield(3,6,0,2,120,180)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc

        $this->addLeftSystem(new Thruster(3, 15, 0, 5, 3));
		$this->addLeftSystem(new EMShield(3,6,0,2,240,300)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
		$this->addLeftSystem(new Hangar(3, 12));
		$this->addLeftSystem(new Hangar(3, 12));
        $this->addLeftSystem(new CWQuadTurbolaser(3, 8, 4, 240, 360));
        $this->addLeftSystem(new CWPointDefenseLaser(2, 4, 1, 180, 360));
        $this->addLeftSystem(new CWPointDefenseLaser(2, 4, 1, 180, 360));
        $this->addLeftSystem(new CWPointDefenseLaser(2, 4, 1, 180, 360));
        $this->addLeftSystem(new CWPointDefenseLaser(2, 4, 1, 180, 360));
        $this->addLeftSystem(new CWQuadTurbolaser(3, 8, 4, 180, 300));

        $this->addRightSystem(new Thruster(3, 15, 0, 6, 4));
		$this->addRightSystem(new EMShield(3,6,0,2,60,120)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
		$this->addRightSystem(new Hangar(3, 12));
		$this->addRightSystem(new Hangar(3, 12));
		$this->addRightSystem(new CWTwinTurbolaser(3, 6, 3, 0, 120));
        $this->addRightSystem(new CWPointDefenseLaser(2, 4, 1, 0, 180));
        $this->addRightSystem(new CWPointDefenseLaser(2, 4, 1, 0, 180));
        $this->addRightSystem(new CWPointDefenseLaser(2, 4, 1, 0, 180));
        $this->addRightSystem(new CWPointDefenseLaser(2, 4, 1, 0, 180));
		$this->addRightSystem(new CWTwinTurbolaser(3, 6, 3, 60, 180));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(3, 70));
        $this->addAftSystem(new Structure(3, 80));
        $this->addLeftSystem(new Structure(3, 90));
        $this->addRightSystem(new Structure(3, 90));
        $this->addPrimarySystem(new Structure(4, 60));
		
		$this->hitChart = array(
			0=> array(
					7 => "Structure",
					8 => "Point Defense Laser",
					10 => "Jump Engine",
					14 => "Scanner",
					16 => "Engine",
					18 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					5 => "Thruster",
					6 => "EM Shield",
					9 => "Point Defense Laser",
					11 => "Quad Turbolaser",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					7 => "EM Shield",
					9 => "Point Defense Laser",
					11 => "Quad Turbolaser",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					5 => "EM Shield",
					7 => "Hangar",
					9 => "Point Defense Laser",
					11 => "Quad Turbolaser",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					5 => "EM Shield",
					7 => "Hangar",
					9 => "Point Defense Laser",
					11 => "Quad Turbolaser",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
