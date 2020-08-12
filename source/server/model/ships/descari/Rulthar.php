<?php
class Rulthar extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 450;
        $this->faction = "Descari";
        $this->phpclass = "Rulthar";
        $this->imagePath = "img/ships/DescariRulpa.png";
        $this->shipClass = "Rulthar Missile Destroyer";
	    $this->isd = 2237;
        $this->limited = 33;	
        
        
        $this->forwardDefense = 12;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 30;
        
         
        $this->addPrimarySystem(new Reactor(4, 13, 0, 0));
        $this->addPrimarySystem(new CnC(5, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 15, 3, 6));
        $this->addPrimarySystem(new Engine(4, 14, 0, 10, 2));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new Thruster(4, 15, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(4, 15, 0, 4, 4));   
        
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));        
        $this->addFrontSystem(new SMissileRack(3, 6, 0, 240, 360));
        $this->addFrontSystem(new SMissileRack(3, 6, 0, 0, 120));
        $this->addFrontSystem(new SMissileRack(3, 6, 0, 180, 60));
        $this->addFrontSystem(new SMissileRack(3, 6, 0, 300, 180));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 60));
	
		$this->addAftSystem(new Thruster(4, 6, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 6, 0, 3, 2));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 120, 300));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 120, 300));       
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 60, 240));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 60, 240));       
        $this->addAftSystem(new Thruster(4, 6, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 6, 0, 3, 2));
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 50));
        $this->addAftSystem(new Structure( 4, 50));
        $this->addPrimarySystem(new Structure( 4, 34));
		
			
		$this->hitChart = array(
			0=> array(
				7 => "Structure",
				11 => "Thruster",
				13 => "Scanner",
				15 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				5 => "Thruster",
				10 => "Class-S Missile Rack",
				12 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				7 => "Thruster",
				11 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
		); 
    }
}



?>
