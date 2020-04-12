<?php
class Looter extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
  		$this->pointCost = 340;
  		$this->faction = "Raiders";
        $this->phpclass = "Looter";
        $this->imagePath = "img/ships/battlewagon.png"; //needs to be changed
        $this->shipClass = "Looter Support Cruiser";
        $this->shipSizeClass = 3;
        $this->fighters = array("normal"=>12);
        
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
		$this->addPrimarySystem(new JumpEngine(4, 12, 3, 40));
		
        $this->addFrontSystem(new Thruster(3, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 6, 0, 2, 1));
		$this->addFrontSystem(new LightParticleCannon(2, 6, 5, 240, 60));
		$this->addFrontSystem(new MediumPlasma(2, 5, 3, 240, 120));
		$this->addFrontSystem(new LightParticleCannon(2, 6, 5, 300, 120));
        
        $this->addAftSystem(new Thruster(3, 10, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 3, 2));
        $this->addAftSystem(new CargoBay(2, 15));
        $this->addAftSystem(new CargoBay(2, 15));
        $this->addAftSystem(new Engine(4, 14, 0, 9, 3));
        $this->addAftSystem(new LightParticleCannon(2, 6, 5, 120, 240));
        
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
        $this->addPrimarySystem(new Structure( 3, 36));
        
        $this->hitChart = array(
        		0=> array(
        				9 => "Structure",
        				11 => "Scanner",
        				13 => "Jump Engine",
        				18 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				5 => "Thruster",
        				8 => "Light Particle Cannon",
        				10 => "Medium Plasma Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				7 => "Light Particle Cannon",
        				9 => "Engine",
        				11 => "Cargo Bay",
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
