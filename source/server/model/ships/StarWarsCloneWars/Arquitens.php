<?php
class Arquitens extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 450;
        $this->faction = "Star Wars Clone Wars";
        $this->phpclass = "Arquitens";
        $this->imagePath = "img/starwars/ArquitensLightCruiser.png";
		$this->canvasSize = 200; //img has 200px per side
        $this->shipClass = "Republic Arquitens Light Cruiser";
		$this->unofficial = true;
		
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 6*5;
         
        $this->addPrimarySystem(new Reactor(5, 15, 0, 0));
        $this->addPrimarySystem(new CnC(5, 10, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 5, 7));
        $this->addPrimarySystem(new Engine(4, 14, 0, 9, 3));
        $this->addPrimarySystem(new Hangar(2, 4));
		$this->addPrimarySystem(new JumpEngine(4, 12, 3, 30));
        $this->addPrimarySystem(new CWConcussionMissile(3, 6, 0, 240, 360));
        $this->addPrimarySystem(new CWConcussionMissile(3, 6, 0, 0, 120));
        $this->addPrimarySystem(new Thruster(3, 12, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(3, 12, 0, 5, 4));
      
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
		$this->addFrontSystem(new EMShield(3,6,0,3,270,90)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
		$this->addFrontSystem(new CWTwinTurbolaser(3, 6, 3, 240, 360));
		$this->addFrontSystem(new CWTwinTurbolaser(3, 6, 3, 240, 360));
		$this->addFrontSystem(new CWTwinTurbolaser(3, 6, 3, 0, 120));
		$this->addFrontSystem(new CWTwinTurbolaser(3, 6, 3, 0, 120));
                
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
		$this->addAftSystem(new EMShield(3,6,0,2,180,360)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
        $this->addAftSystem(new CWPointDefenseLaser(2, 4, 1, 180, 360));
        $this->addAftSystem(new CWPointDefenseLaser(2, 4, 1, 180, 360));
        $this->addAftSystem(new CWPointDefenseLaser(2, 4, 1, 0, 180));
        $this->addAftSystem(new CWPointDefenseLaser(2, 4, 1, 0, 180));
		$this->addAftSystem(new EMShield(3,6,0,2,0,180)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
       
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 40));
        $this->addAftSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 5, 60));
		
        $this->hitChart = array(
            0=> array(
                    7 => "Structure",
					9 => "Thruster",
					10 => "Jump Engine",
					12 => "Concussion Missile",
                    14 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    6 => "Thruster",
                    8 => "EM Shield",
					11 => "Twin Turbolaser",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    7 => "Thruster",
					10 => "Point Defense Laser",
                    12 => "EM Shield",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
