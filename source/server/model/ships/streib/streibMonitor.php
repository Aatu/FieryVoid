<?php
class StreibMonitor extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 675;
	$this->faction = "Streib";
        $this->phpclass = "StreibMonitor";
        $this->imagePath = "img/ships/streibmonitor.png";
        $this->shipClass = "Monitor";
        $this->shipSizeClass = 2;
        
        $this->isd = 2216;
		$this->unofficial = true;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 14;
        $this->fighters = array("shuttles" => 2); 
       
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->gravitic = true;	

        $this->addPrimarySystem(new Thruster(8, 8, 0, 5, 3)); 
        $this->addPrimarySystem(new Thruster(8, 8, 0, 5, 4));         
        $this->addPrimarySystem(new DualBurstBeam(5, 8, 5, 120, 360));
        $this->addPrimarySystem(new SurgeLaser(5, 0, 0, 0, 240));
        $this->addPrimarySystem(new Engine(8, 10, 0, 9, 2));          
        $this->addPrimarySystem(new Reactor(8, 13, 0, 0));
        $this->addPrimarySystem(new CnC(9, 10, 0, 0));        

        $this->addFrontSystem(new Scanner(7, 10, 5, 10));
        $this->addFrontSystem(new Hangar(6, 2, 0));
        $this->addFrontSystem(new ImprovedBlastLaser(7, 10, 8, 300, 60));
        $this->addFrontSystem(new EMWaveDisruptor(6, 8, 5, 270, 90));
        $this->addFrontSystem(new SurgeLaser(5, 0, 0, 240, 60));
        $this->addFrontSystem(new SurgeLaser(5, 0, 0, 240, 60));
        $this->addFrontSystem(new BurstPulseCannon(7, 6, 6, 300, 60));
        $this->addFrontSystem(new Thruster(7, 5, 0, 4, 1));
        $this->addFrontSystem(new Thruster(7, 5, 0, 4, 1));	        
        
        $this->addAftSystem(new Thruster(8, 4, 0, 3, 2));
        $this->addAftSystem(new Thruster(8, 8, 0, 6, 2));
		$this->addAftSystem(new TractorBeam(6, 4, 0, 0));
        $this->addAftSystem(new CargoBay(7, 50));
 

        //0:primary, 1:front, 2:rear,
        $this->addFrontSystem(new Structure(8, 36));
        $this->addAftSystem(new Structure(8, 32));
        $this->addPrimarySystem(new Structure(8, 36));
	    
        $this->hitChart = array(
            0 => array(
                8 => "Structure",
                12 => "Thruster",
                13 => "Dual Burst Beam",
                14 => "Surge Laser",
                16 => "Engine",
                18 => "Reactor", 
                20 => "C&C",
            ),
            1 => array(
                3 => "Thruster",
                5 => "Improved Blast Laser",
                6 => "Burst Pulse Cannon",
                8 => "Surge Laser",
                9 => "EM-Wave Disruptor",
                11 => "Scanner",
                12 => "Hangar",
                18 => "Structure",
                20 => "Primary",
            ),
            2 => array(
                6 => "Thruster",
                7 => "Tractor Beam",
                10 => "Cargo Bay",
                18 => "Structure",
                20 => "Primary",
            ),
        );
    }
}
?>
