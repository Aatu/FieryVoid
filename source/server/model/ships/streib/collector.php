<?php
class Collector extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 1100;
		$this->faction = "Streib";
        $this->phpclass = "Collector";
        $this->imagePath = "img/ships/collector.png";
        $this->shipClass = "Collector";
        $this->shipSizeClass = 3;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;		
        
         
        $this->addPrimarySystem(new Reactor(5, 25, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 18, 3, 6));
        $this->addPrimarySystem(new Engine(6, 18, 0, 7, 3));
		$this->addPrimarySystem(new Hangar(5, 8));
        $this->addPrimarySystem(new DualBurstBeam(2, 4, 1, 0, 360));
		$this->addPrimarySystem(new MediumBurstBeam(2, 4, 1, 0, 360));
        $this->addPrimarySystem(new HeavyBurstBeam(2, 4, 1, 0, 360));
        $this->addPrimarySystem(new BurstPulseCannon(2, 4, 1, 0, 360));
        
		
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));

        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        
		$this->addLeftSystem(new Thruster(3, 13, 0, 5, 3));
		
		$this->addRightSystem(new Thruster(3, 13, 0, 5, 4));
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 52));
        $this->addAftSystem(new Structure( 4, 42));
        $this->addLeftSystem(new Structure( 4, 60));
        $this->addRightSystem(new Structure( 4, 60));
        $this->addPrimarySystem(new Structure( 5, 54));
    }
}