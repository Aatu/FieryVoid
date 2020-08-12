<?php
class Rulnata extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 475;
        $this->faction = "Descari";
        $this->phpclass = "Rulnata";
        $this->imagePath = "img/ships/DescariRulnata.png";
        $this->shipClass = "Rulnata Scout";
	    $this->isd = 2245;
        $this->limited = 33;	    
        
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 30;
        
         
        $this->addPrimarySystem(new Reactor(5, 20, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new ElintScanner(5, 18, 9, 4));
        $this->addPrimarySystem(new Engine(5, 16, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(5, 2));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 4, 4));
        $this->addPrimarySystem(new SMissileRack(3, 6, 0, 180, 60));
        $this->addPrimarySystem(new SMissileRack(3, 6, 0, 300, 180));   
        
        
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new ElintScanner(4, 8, 4, 2));
        $this->addFrontSystem(new ElintScanner(4, 8, 4, 2));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 60));        
		
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 0, 180)); 
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));       
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 36));
        $this->addAftSystem(new Structure( 4, 36));
        $this->addPrimarySystem(new Structure( 5, 48));
		
			
		
		$this->hitChart = array(
			0=> array(
				6 => "Structure",
				10 => "Thruster",
				12 => "Class-S Missile Rack",
				14 => "Scanner",
				16 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				4 => "Thruster",
				8 => "Scanner",
				10 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				6 => "Thruster",
				10 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
		); 
    }
}



?>