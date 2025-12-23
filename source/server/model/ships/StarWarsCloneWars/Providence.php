<?php
class Providence extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 1000;
	$this->faction = "Star Wars Clone Wars";
        $this->phpclass = "Providence";
        $this->imagePath = "img/starwars/CloneWars/providence.png";
        $this->shipClass = "Separatist Providence Destroyer";
        $this->shipSizeClass = 3;
		$this->canvasSize = 200; //img has 200px per side
		$this->unofficial = true;
//        $this->limited = 33;

        $this->fighters = array("normal"=>6, "light"=>36);

//		$this->isd = 2050;
        
        $this->forwardDefense = 16;
        $this->sideDefense = 18;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 5;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(5, 24, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 20, 5, 8));
        $this->addPrimarySystem(new Engine(5, 20, 0, 10, 4));
		$this->addPrimarySystem(new Hangar(4, 10));
		$this->addPrimarySystem(new JumpEngine(4, 24, 3, 28));

        $this->addFrontSystem(new Thruster(3, 12, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 12, 0, 4, 1));
        $this->addFrontSystem(new CWProtonTorpedo(3, 5, 3, 240, 360));
        $this->addFrontSystem(new CWHeavyIonCannon(3, 8, 4, 300, 60));
        $this->addFrontSystem(new CWPointDefenseLaser(2, 4, 1, 240, 60));
        $this->addFrontSystem(new CWQuadTurbolaser(3, 8, 4, 300, 60));
        $this->addFrontSystem(new CWPointDefenseLaser(2, 4, 1, 300, 120));
        $this->addFrontSystem(new CWHeavyIonCannon(3, 8, 4, 300, 60));
        $this->addFrontSystem(new CWProtonTorpedo(3, 5, 3, 0, 120));

        $this->addAftSystem(new Thruster(3, 9, 0, 9, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 9, 2));
		$this->addAftSystem(new EMShield(3,6,0,2,180,300)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
        $this->addAftSystem(new CWPointDefenseLaser(2, 4, 1, 120, 300));
        $this->addAftSystem(new CWQuadTurbolaser(3, 8, 4, 180, 300));
        $this->addAftSystem(new CWQuadTurbolaser(3, 8, 4, 60, 180));
        $this->addAftSystem(new CWPointDefenseLaser(2, 4, 1, 60, 240));
		$this->addAftSystem(new EMShield(3,6,0,2,60,180)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc

        $this->addLeftSystem(new Thruster(3, 15, 0, 5, 3));
		$this->addLeftSystem(new EMShield(3,6,0,3,240,360)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
		$this->addLeftSystem(new Hangar(4, 18));
        $this->addLeftSystem(new CWQuadTurbolaser(3, 8, 4, 180, 360));
        $this->addLeftSystem(new CWQuadTurbolaser(3, 8, 4, 180, 360));
        $this->addLeftSystem(new CWProtonTorpedo(3, 5, 3, 240, 360));
        $this->addLeftSystem(new CWProtonTorpedo(3, 5, 3, 240, 360));

        $this->addRightSystem(new Thruster(3, 15, 0, 5, 4));
		$this->addRightSystem(new EMShield(3,6,0,3,0,120)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
		$this->addRightSystem(new Hangar(4, 18));
        $this->addRightSystem(new CWQuadTurbolaser(3, 8, 4, 0, 180));
        $this->addRightSystem(new CWQuadTurbolaser(3, 8, 4, 0, 180));
        $this->addRightSystem(new CWProtonTorpedo(3, 5, 3, 0, 120));
        $this->addRightSystem(new CWProtonTorpedo(3, 5, 3, 0, 120));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(5, 60));
        $this->addAftSystem(new Structure(4, 50));
        $this->addLeftSystem(new Structure(4, 70));
        $this->addRightSystem(new Structure(4, 70));
        $this->addPrimarySystem(new Structure(5, 60));
		
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
					6 => "Quad Turbolaser",
					7 => "Point Defense Laser",
					9 => "Heavy Ion Cannon",
					11 => "Proton Torpedo",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					9 => "Quad Turbolaser",
					10 => "Point Defense Laser",
					11 => "EM Shield",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					5 => "EM Shield",
					7 => "Hangar",
					10 => "Quad Turbolaser",
					12 => "Proton Torpedo",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					5 => "EM Shield",
					7 => "Hangar",
					10 => "Quad Turbolaser",
					12 => "Proton Torpedo",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
