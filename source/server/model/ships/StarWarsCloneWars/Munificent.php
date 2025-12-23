<?php
class Munificent extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 600;
        $this->faction = "Star Wars Clone Wars";
        $this->phpclass = "Munificent";
        $this->imagePath = "img/starwars/CloneWars/Munificent.png";
		$this->canvasSize = 200; //img has 200px per side
        $this->shipClass = "Separatist Munificent Star Frigate";
		$this->unofficial = true;
		
		$this->fighters = array("normal"=>12);
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 6*5;
         
        $this->addPrimarySystem(new Reactor(5, 18, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 5, 6));
        $this->addPrimarySystem(new Engine(5, 15, 0, 9, 4));
        $this->addPrimarySystem(new Hangar(4, 14));
		$this->addPrimarySystem(new JumpEngine(4, 16, 3, 30));
        $this->addPrimarySystem(new CWPointDefenseLaser(2, 4, 1, 180, 360));
        $this->addPrimarySystem(new CWPointDefenseLaser(2, 4, 1, 0, 180));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 5, 4));
      
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
		$this->addFrontSystem(new EMShield(3,6,0,2,240,360)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
        $this->addFrontSystem(new CWPointDefenseLaser(2, 4, 1, 270, 90));
		$this->addFrontSystem(new CWTwinTurbolaser(2, 6, 3, 240, 360));
        $this->addFrontSystem(new CWHeavyIonCannon(3, 8, 4, 300, 60));
        $this->addFrontSystem(new CWHeavyTurbolaser(3, 6, 6, 300, 60));
        $this->addFrontSystem(new CWHeavyTurbolaser(3, 6, 6, 300, 60));
        $this->addFrontSystem(new CWHeavyIonCannon(3, 8, 4, 300, 60));
		$this->addFrontSystem(new CWTwinTurbolaser(2, 6, 3, 0, 120));
        $this->addFrontSystem(new CWPointDefenseLaser(2, 4, 1, 270, 90));
		$this->addFrontSystem(new EMShield(3,6,0,2,0,120)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
                
        $this->addAftSystem(new Thruster(3, 6, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 6, 0, 2, 2));
		$this->addAftSystem(new CWTwinTurbolaser(2, 6, 3, 180, 300));
		$this->addAftSystem(new CWTwinTurbolaser(2, 6, 3, 180, 300));
        $this->addAftSystem(new CWPointDefenseLaser(2, 4, 1, 90, 270));
		$this->addAftSystem(new EMShield(3,6,0,2,90,270)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
        $this->addAftSystem(new CWPointDefenseLaser(2, 4, 1, 90, 270));
		$this->addAftSystem(new CWTwinTurbolaser(2, 6, 3, 60, 180));
		$this->addAftSystem(new CWTwinTurbolaser(2, 6, 3, 60, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 55));
        $this->addAftSystem(new Structure( 4, 45));
        $this->addPrimarySystem(new Structure( 5, 60));
		
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
					9 => "Point Defense Laser",
					11 => "Thruster",
                    13 => "Scanner",
                    15 => "Engine",
                    16 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    3 => "Thruster",
                    4 => "Point Defense Laser",
                    5 => "EM Shield",
					7 => "Heavy Turbolaser",
					9 => "Twin Turbolaser",
					11 => "Heavy Ion Cannon",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    5 => "Thruster",
					6 => "EM Shield",
					8 => "Point Defense Laser",
                    12 => "Twin Turbolaser",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
