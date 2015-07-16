<?php
class Soladon extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 475;
        $this->faction = "The Lion";
        $this->phpclass = "Soladon";
        $this->imagePath = "img/ships/celerian.png";
        $this->shipClass = "Soladon Escort Cruiser";
        $this->shipSizeClass = 3;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;        
         
        $this->addPrimarySystem(new Reactor(6, 16, 0, 0));
        $this->addPrimarySystem(new CnC(6, 14, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 22, 4, 8));
        $this->addPrimarySystem(new Engine(5, 18, 0, 9, 3));
		$this->addPrimarySystem(new Hangar(4, 2));		
        
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new ParticleProjector(3, 6, 1, 240, 60));
        $this->addFrontSystem(new ParticleProjector(3, 6, 1, 240, 60));
        $this->addFrontSystem(new ParticleProjector(3, 6, 1, 300, 120));
        $this->addFrontSystem(new ParticleProjector(3, 6, 1, 300, 120));
        $this->addFrontSystem(new ParticleProjector(3, 6, 1, 300, 60));
		
        $this->addAftSystem(new Thruster(3, 10, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 3, 2));
        $this->addAftSystem(new JumpEngine(5, 25, 3, 20));
        $this->addAftSystem(new ParticleProjector(3, 6, 1, 60, 240));
        $this->addAftSystem(new ParticleProjector(3, 6, 1, 90, 270));
        $this->addAftSystem(new ParticleProjector(3, 6, 1, 120, 300));

		$this->addLeftSystem(new Thruster(4, 14, 0, 5, 3));
        $this->addLeftSystem(new ParticleProjector(3, 6, 1, 240, 360));
        $this->addLeftSystem(new ParticleProjector(3, 6, 1, 180, 360));
        $this->addLeftSystem(new ParticleProjector(3, 6, 1, 180, 360));        
        		
		$this->addRightSystem(new Thruster(4, 14, 0, 5, 4));
        $this->addRightSystem(new ParticleProjector(3, 6, 1, 0, 180));
        $this->addRightSystem(new ParticleProjector(3, 6, 1, 0, 180));
        $this->addRightSystem(new ParticleProjector(3, 6, 1, 0, 120));
	
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 38));
        $this->addAftSystem(new Structure( 4, 36));
        $this->addLeftSystem(new Structure( 4, 40));
        $this->addRightSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 5, 40));
		
		
    }

}



?>
