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
        $this->gravitic = true;	
        
         
        $this->addPrimarySystem(new Reactor(9, 16, 0, 0));
        $this->addPrimarySystem(new Engine(9, 12, 0, 10, 2));
        $this->addPrimarySystem(new TractorBeam(8, 4, 0, 0));

        $this->addFrontSystem(new Thruster(8, 6, 0, 5, 1));
        $this->addFrontSystem(new Thruster(8, 6, 0, 5, 1));
        $this->addFrontSystem(new MediumBurstBeam(6, 7, 6, 240, 60));
        $this->addFrontSystem(new BurstPulseCannon(7, 6, 6, 300, 60));
        $this->addFrontSystem(new MediumBurstBeam(6, 7, 6, 300, 120));

        $this->addAftSystem(new Thruster(9, 8, 0, 7, 2));
        $this->addAftSystem(new Thruster(9, 8, 0, 7, 2));
        $this->addAftSystem(new MediumBurstBeam(6, 7, 6, 60, 240));
        $this->addAftSystem(new ImprovedBlastLaser(7, 10, 8, 120, 240));
        $this->addAftSystem(new MediumBurstBeam(6, 7, 6, 120, 300));

        $this->addLeftSystem(new Thruster(3, 13, 0, 5, 4));
        $this->addLeftSystem(new JumpEngine(9, 10, 4, 10));
        $this->addLeftSystem(new CargoBay(9, 9));
        $this->addLeftSystem(new ImprovedBlastLaser(7, 10, 8, 300, 60));
        $this->addLeftSystem(new HeavyBurstBeam(7, 9, 8, 300, 60));
        $this->addLeftSystem(new DualBurstBeam(5, 8, 5, 180, 60));
        $this->addLeftSystem(new EMWaveDisruptor(6, 8, 5, 180, 60));

        $this->addRightSystem(new Thruster(3, 13, 0, 5, 3));
        $this->addRightSystem(new Hangar(8, 6, 0));
        $this->addRightSystem(new CnC(9, 12, 0, 0));
        $this->addRightSystem(new Scanner(9, 8, 5, 12));
        $this->addRightSystem(new CargoBay(9, 9));
        $this->addRightSystem(new DualBurstBeam(5, 8, 5, 300, 180));
        $this->addRightSystem(new EMWaveDisruptor(6, 8, 4, 300, 180));





   /*  
        $this->addPrimarySystem(new ImprovedBlastLaser(2, 4, 1, 0, 360));
        $this->addPrimarySystem(new DualBurstBeam(2, 4, 1, 0, 360));
		$this->addPrimarySystem(new MediumBurstBeam(2, 4, 1, 0, 360));
        $this->addPrimarySystem(new HeavyBurstBeam(2, 4, 1, 0, 360));
        $this->addPrimarySystem(new BurstPulseCannon(2, 4, 1, 0, 360));
     */   
		
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(9, 40));
        $this->addAftSystem(new Structure(9, 33));
        $this->addLeftSystem(new Structure(9, 40));
        $this->addRightSystem(new Structure(9, 40));
        $this->addPrimarySystem(new Structure(9, 40));
    }
}