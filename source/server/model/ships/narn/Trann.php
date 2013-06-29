<?php
class Trann extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 575;
	$this->faction = "Narn";
        $this->phpclass = "Trann";
        $this->imagePath = "img/ships/tloth.png";
        $this->shipClass = "T'Rann Heavy Carrier";
        $this->shipSizeClass = 3;
        $this->occurence = "uncommon";
        $this->fighters = array("normal"=>24);
        
        $this->forwardDefense = 16;
        $this->sideDefense = 18;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 2;

        $this->addPrimarySystem(new Reactor(5, 21, 0, 2));
        $this->addPrimarySystem(new CnC(5, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 24, 4, 8));
        $this->addPrimarySystem(new Engine(5, 20, 0, 12, 3));
	$this->addPrimarySystem(new JumpEngine(5, 24, 3, 20));
	$this->addPrimarySystem(new Hangar(5, 26));
        
        $this->addFrontSystem(new LightPulse(2, 4, 2, 270, 90));
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new LightPulse(2, 4, 2, 270, 90));
      
	$this->addAftSystem(new LightPulse(2, 4, 2, 90, 270));
	$this->addAftSystem(new LightPulse(2, 4, 2, 90, 270));
        $this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
	$this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
	$this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
	$this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
        
	$this->addLeftSystem(new LightPulse(2, 4, 2, 180, 0));
	$this->addLeftSystem(new MediumPulse(5, 6, 3, 300, 0));
	$this->addLeftSystem(new MediumPulse(5, 6, 3, 300, 0));
        $this->addLeftSystem(new Thruster(4, 15, 0, 4, 3));
              
	$this->addRightSystem(new LightPulse(2, 4, 2, 0, 180));
	$this->addRightSystem(new MediumPulse(5, 6, 3, 0, 60));
	$this->addRightSystem(new MediumPulse(5, 6, 3, 0, 60));
	$this->addRightSystem(new Thruster(4, 15, 0, 4, 4));

        $this->addFrontSystem(new Structure(4, 44));
        $this->addAftSystem(new Structure(4, 48));
        $this->addLeftSystem(new Structure(4, 62));
        $this->addRightSystem(new Structure(4, 62));
        $this->addPrimarySystem(new Structure(5, 45));
    }
}

?>
