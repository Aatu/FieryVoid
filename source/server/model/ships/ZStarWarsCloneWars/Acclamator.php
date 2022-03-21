<?php
class Acclamator extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 700;
	$this->faction = "ZStarWars Clone Wars";
        $this->phpclass = "Acclamator";
        $this->imagePath = "img/starwars/CloneWars/Acclamator.png";
        $this->shipClass = "Acclamator Assault Ship";
        $this->shipSizeClass = 3;
		$this->canvasSize = 155; 
		$this->unofficial = true;
//        $this->limited = 33;

        $this->fighters = array("normal"=>70);

//		$this->isd = 2050;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 1.0;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(5, 25, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 20, 5, 7));
        $this->addPrimarySystem(new Engine(5, 18, 0, 8, 4));
		$this->addPrimarySystem(new Hangar(4, 4));
		$this->addPrimarySystem(new JumpEngine(4, 16, 3, 24));

        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new CWQuadTurbolaser(3, 8, 4, 240, 360));
        $this->addFrontSystem(new CWPointDefenseLaser(2, 4, 1, 240, 60));
        $this->addFrontSystem(new CWPointDefenseLaser(2, 4, 1, 300, 120));
        $this->addFrontSystem(new CWQuadTurbolaser(3, 8, 4, 0, 120));

        $this->addAftSystem(new Thruster(3, 4, 0, 1, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 4, 0, 1, 2));
        $this->addAftSystem(new CWPointDefenseLaser(2, 4, 1, 90, 270));
		$this->addAftSystem(new CWShield(4,6,0,3,180,300)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
		$this->addAftSystem(new CWShield(4,6,0,3,60,180)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
        $this->addAftSystem(new CWPointDefenseLaser(2, 4, 1, 90, 270));

        $this->addLeftSystem(new Thruster(3, 13, 0, 5, 3));
		$this->addLeftSystem(new CWShield(4,6,0,3,240,360)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
        $this->addLeftSystem(new CWConcussionMissile(4, 6, 0, 240, 360));
		$this->addLeftSystem(new CWQuadTurbolaser(3, 8, 4, 180, 360));
		$this->addLeftSystem(new CWQuadTurbolaser(3, 8, 4, 180, 360));

        $this->addRightSystem(new Thruster(3, 13, 0, 5, 4));
		$this->addRightSystem(new CWShield(4,6,0,3,0,120)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
        $this->addRightSystem(new CWConcussionMissile(4, 6, 0, 0, 120));
		$this->addRightSystem(new CWQuadTurbolaser(3, 8, 4, 0, 180));
		$this->addRightSystem(new CWQuadTurbolaser(3, 8, 4, 0, 180));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 40));
        $this->addAftSystem(new Structure(4, 40));
        $this->addLeftSystem(new Structure(4, 50));
        $this->addRightSystem(new Structure(4, 50));
        $this->addPrimarySystem(new Structure(5, 40));
		
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
					5 => "Thruster",
					7 => "Twin Turbolaser",
					10 => "Point Defense Laser",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Shield",
					10 => "Point Defense Laser",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					5 => "Shield",
					6 => "Proton Torpedo",
					8 => "Point Defense Laser",
					11 => "Twin Heavy Turbolaser",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					5 => "Shield",
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
