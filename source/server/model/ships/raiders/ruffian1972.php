<?php
class Ruffian1972 extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
  		$this->pointCost = 320;
  		$this->faction = "Raiders";
        $this->phpclass = "Ruffian1972";
        $this->imagePath = "img/ships/battlewagon.png"; //needs to be changed
        $this->shipClass = "Ruffian Cruiser (1972)";
			$this->variantOf = "Ruffian Cruiser";	    
			$this->occurence = "common";
        $this->shipSizeClass = 3;
        $this->fighters = array("normal"=>18);
        
		$this->notes = "Generic raider unit.";
		$this->notes .= "<br> ";

		$this->isd = 1972;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1.5;
        $this->accelcost = 3;
        $this->rollcost = 1;
        $this->pivotcost = 5;
        $this->iniativebonus = -5;
         
        $this->addPrimarySystem(new Reactor(4, 16, 0, 0));
        $this->addPrimarySystem(new CnC(5, 15, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 3, 4));
        $this->addPrimarySystem(new Hangar(3, 13));
		$this->addPrimarySystem(new CargoBay(2, 15));
		$this->addPrimarySystem(new CargoBay(2, 15));
		
        $this->addFrontSystem(new Thruster(3, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 6, 0, 2, 1));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
		$this->addFrontSystem(new StrikeLaser(2, 7, 4, 240, 120));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));
        
        $this->addAftSystem(new Thruster(3, 10, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 3, 2));
        $this->addAftSystem(new Hangar(2, 3));
        $this->addAftSystem(new Hangar(2, 3));
        $this->addAftSystem(new Engine(4, 14, 0, 9, 3));
        
        $this->addLeftSystem(new Thruster(3, 15, 0, 4, 3));
		$this->addLeftSystem(new LightParticleCannon(2, 6, 5, 180, 360));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
		
		$this->addRightSystem(new Thruster(3, 15, 0, 4, 4));
        $this->addRightSystem(new LightParticleCannon(2, 6, 5, 0, 180));
        $this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 32));
        $this->addAftSystem(new Structure( 3, 36));
        $this->addLeftSystem(new Structure( 3, 30));
        $this->addRightSystem(new Structure( 3, 30));
        $this->addPrimarySystem(new Structure( 4, 30));
        
        $this->hitChart = array(
        		0=> array(
        				10 => "Structure",
        				12 => "Scanner",
        				14 => "Cargo Bay",
						16 => "Hangar",
        				18 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				5 => "Thruster",
        				8 => "Strike Laser",
						10 => "Light Particle Beam",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				8 => "Engine",
        				10 => "Hangar",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				6 => "Thruster",
        				7 => "Light Particle Beam",
        				9 => "Light Particle Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				6 => "Thruster",
        				7 => "Light Particle Beam",
        				9 => "Light Particle Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
