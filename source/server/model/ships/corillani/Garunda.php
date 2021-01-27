<?php
class Garunda extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 525;
        $this->faction = "Corillani";
        $this->phpclass = "Garunda";
        $this->imagePath = "img/ships/CorillaniGarunda.png";
        $this->shipClass = "Garunda Destroyer";
	    $this->isd = 2227;
		$this->notes = 'Corillani Peoples Navy (CPN)';
		$this->canvasSize= 200;
        $this->fighters = array("normal"=>6);			    
        
        
        $this->forwardDefense = 13;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 30;
        
         
        $this->addPrimarySystem(new Reactor(4, 17, 0, 0));
        $this->addPrimarySystem(new CnC(4, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 3, 5));
        $this->addPrimarySystem(new Engine(4, 18, 0, 12, 3));
        $this->addPrimarySystem(new Hangar(2, 7));
        $this->addPrimarySystem(new Thruster(4, 8, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(3, 5, 0, 2, 3));        
        $this->addPrimarySystem(new Thruster(3, 5, 0, 2, 4)); 
        $this->addPrimarySystem(new Thruster(4, 8, 0, 3, 4));
        $this->addPrimarySystem(new SMissileRack(3, 6, 0, 180, 360));
		$this->addPrimarySystem(new SMissileRack(3, 6, 0, 0, 180));                   
        
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new ParticleCannon(3, 8, 7, 300, 60));
        $this->addFrontSystem(new ParticleCannon(3, 8, 7, 300, 60));
        $this->addFrontSystem(new TwinArray(2, 6, 2, 240, 60));
        $this->addFrontSystem(new TwinArray(2, 6, 2, 300, 120));

        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));        
        $this->addAftSystem(new TwinArray(2, 6, 2, 120, 300));
        $this->addAftSystem(new TwinArray(2, 6, 2, 60, 240));       
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 37));
        $this->addAftSystem(new Structure( 4, 39));
        $this->addPrimarySystem(new Structure( 4, 40));
		
			
		
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
				5 => "Thruster",
				9 => "Particle Cannon",
				12 => "Twin Array",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				7 => "Thruster",
				9 => "Twin Array",
				18 => "Structure",
				20 => "Primary",
			),
		); 
    }
}



?>
