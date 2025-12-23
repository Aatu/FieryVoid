<?php
class Venator extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 900;
	$this->faction = "Star Wars Clone Wars";
        $this->phpclass = "Venator";
        $this->imagePath = "img/starwars/CloneWars/Venator.png";
        $this->shipClass = "Republic Venator Attack Destroyer";
        $this->shipSizeClass = 3;
		$this->canvasSize = 200; //img has 200px per side
		$this->unofficial = true;
//        $this->limited = 33;

        $this->fighters = array("normal"=>72);

//		$this->isd = 2050;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 4;
        $this->rollcost = 4;
        $this->pivotcost = 4;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(5, 25, 0, 0));
        $this->addPrimarySystem(new CnC(5, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 20, 5, 8));
        $this->addPrimarySystem(new Engine(5, 20, 0, 8, 4));
		$this->addPrimarySystem(new Hangar(3, 44));
		$this->addPrimarySystem(new JumpEngine(4, 20, 3, 24));

        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new CWTwinTurbolaser(3, 6, 3, 240, 360));
        $this->addFrontSystem(new CWPointDefenseLaser(2, 4, 1, 240, 60));
        $this->addFrontSystem(new CWPointDefenseLaser(2, 4, 1, 270, 90));
        $this->addFrontSystem(new CWPointDefenseLaser(2, 4, 1, 300, 120));
        $this->addFrontSystem(new CWTwinTurbolaser(3, 6, 3, 0, 120));
		$this->addFrontSystem(new Hangar(3, 36));

        $this->addAftSystem(new Thruster(3, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 2, 2));
        $this->addAftSystem(new CWPointDefenseLaser(2, 4, 1, 90, 270));
		$this->addAftSystem(new EMShield(4,6,0,3,180,300)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
		$this->addAftSystem(new EMShield(4,6,0,3,60,180)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
        $this->addAftSystem(new CWPointDefenseLaser(2, 4, 1, 90, 270));

        $this->addLeftSystem(new Thruster(3, 15, 0, 5, 3));
		$this->addLeftSystem(new EMShield(4,6,0,3,240,360)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
        $this->addLeftSystem(new CWProtonTorpedo(4, 5, 3, 240, 360));
		$this->addLeftSystem(new CWTwinHeavyTurbolaser(4, 8, 5, 180, 360));
		$this->addLeftSystem(new CWTwinHeavyTurbolaser(4, 8, 5, 180, 360));
        $this->addLeftSystem(new CWPointDefenseLaser(2, 4, 1, 180, 360));
        $this->addLeftSystem(new CWPointDefenseLaser(2, 4, 1, 180, 360));

        $this->addRightSystem(new Thruster(3, 15, 0, 5, 4));
		$this->addRightSystem(new EMShield(4,6,0,3,0,120)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
        $this->addRightSystem(new CWProtonTorpedo(4, 5, 3, 0, 120));
		$this->addRightSystem(new CWTwinHeavyTurbolaser(4, 8, 5, 0, 180));
		$this->addRightSystem(new CWTwinHeavyTurbolaser(4, 8, 5, 0, 180));
        $this->addRightSystem(new CWPointDefenseLaser(2, 4, 1, 0, 180));
        $this->addRightSystem(new CWPointDefenseLaser(2, 4, 1, 0, 180));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 60));
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
					6 => "Twin Turbolaser",
					8 => "Point Defense Laser",
					10 => "Hangar",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "EM Shield",
					10 => "Point Defense Laser",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					5 => "EM Shield",
					6 => "Proton Torpedo",
					8 => "Point Defense Laser",
					11 => "Twin Heavy Turbolaser",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					5 => "EM Shield",
					6 => "Proton Torpedo",
					8 => "Point Defense Laser",
					11 => "Twin Heavy Turbolaser",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
