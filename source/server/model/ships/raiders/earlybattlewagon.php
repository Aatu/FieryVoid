<?php
class EarlyBattlewagon extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 450;
        $this->faction = "Raiders";
        $this->phpclass = "EarlyBattlewagon";
        $this->imagePath = "img/ships/RaiderEarlyBattlewagon.png";
        $this->shipClass = "Early Battlewagon";
        $this->shipSizeClass = 3;
		$this->fighters = array("cargo shuttles"=>2);   
        $this->fighters = array("light"=>18);
        
		$this->notes = "Generic raider unit.";
		$this->notes .= "<br> ";
		
		$this->unofficial = true;

		$this->isd = 1980;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1.33;
        $this->accelcost = 3;
        $this->rollcost = 1;
        $this->pivotcost = 3;
         
        $this->addPrimarySystem(new Reactor(3, 20, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 12, 3, 5));
        $this->addPrimarySystem(new Engine(3, 12, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(3, 20, 6));
        $this->addPrimarySystem(new CargoBay(2, 45));
        $this->addPrimarySystem(new JumpEngine(5, 15, 4, 24));
  
        $this->addFrontSystem(new Thruster(2, 9, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 9, 0, 2, 1));
        $this->addFrontSystem(new HeavyPlasma(3, 8, 5, 300, 60));
        $this->addFrontSystem(new LightParticleCannon(2, 6, 5, 240, 360));
        $this->addFrontSystem(new LightParticleBeamShip(2, 1, 1, 240, 360));
        $this->addFrontSystem(new LightParticleCannon(2, 6, 5, 0, 120));
        $this->addFrontSystem(new LightParticleBeamShip(2, 1, 1, 0, 120));

        $this->addAftSystem(new LightParticleBeamShip(2, 1, 1, 120, 300));
        $this->addAftSystem(new LightParticleBeamShip(2, 1, 1, 60, 240));
        $this->addAftSystem(new Thruster(3, 9, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 4, 2));
		
		
        
        $this->addLeftSystem(new Thruster(3, 13, 0, 4, 3));
        $this->addLeftSystem(new LightParticleCannon(2, 6, 5, 180, 360));
  
        $this->addRightSystem(new Thruster(3, 13, 0, 4, 4));
        $this->addRightSystem(new LightParticleCannon(2, 6, 5, 0, 180));
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 36));
        $this->addAftSystem(new Structure( 4, 39));
        $this->addLeftSystem(new Structure( 4, 39));
        $this->addRightSystem(new Structure( 4, 39));
        $this->addPrimarySystem(new Structure( 4, 50));
        
        $this->hitChart = array(
        		0=> array(
        				7 => "Structure",
        				8 => "Cargo Bay",
        				10 => "Jump Engine",
        				13 => "Scanner",
        				15 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				4 => "Thruster",
        				6 => "Heavy Plasma Cannon",
        				7 => "Light Particle Cannon",
        				10 => "Light Particle Beam",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				8 => "Light Particle Beam",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				4 => "Thruster",
        				7 => "Light Particle Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				4 => "Thruster",
        				7 => "Light Particle Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
