<?php
class Sakar1980 extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 500;
        $this->faction = "Centauri (WotCR)";
        $this->phpclass = "Sakar1980";
        $this->imagePath = "img/ships/sakar.png"; 
        $this->shipClass = "Sakar Carrier";
        $this->shipSizeClass = 3;
        $this->limited = 33;
        $this->fighters = array("normal"=>48);
	    $this->isd = 1980;
        $this->forwardDefense = 17;
        $this->sideDefense = 17;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 4;
        $this->rollcost = 4;
        $this->pivotcost = 3;
                 
        $this->addPrimarySystem(new Reactor(5, 18, 0, 0));
        $this->addPrimarySystem(new CnC(5, 25, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 4, 7));
        $this->addPrimarySystem(new Engine(5, 20, 0, 10, 3));
		$this->addPrimarySystem(new Hangar(5, 4));
        
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));
        $this->addFrontSystem(new Hangar(4, 12));
        $this->addFrontSystem(new Hangar(4, 12));
        
        $this->addAftSystem(new Thruster(2, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 8, 0, 2, 2));
        $this->addAftSystem(new JumpEngine(5, 24, 3, 20));      
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 120, 240));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270));
        
		$this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));
        $this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        $this->addLeftSystem(new Hangar(4, 12));
        
		$this->addRightSystem(new Thruster(4, 15, 0, 5, 4));
        $this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        $this->addRightSystem(new Hangar(4, 12));
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 56));
        $this->addAftSystem(new Structure( 4, 48));
        $this->addLeftSystem(new Structure( 4, 48));
        $this->addRightSystem(new Structure( 4, 48));
        $this->addPrimarySystem(new Structure( 5, 48));
    }
}
?>
