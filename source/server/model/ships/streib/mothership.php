<?php
class Mothership extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 1400;
	$this->faction = "Streib";
        $this->phpclass = "Mothership";
        $this->imagePath = "img/ships/streibmothership.png";
        $this->shipClass = "Mothership";
        $this->shipSizeClass = 3;
        
        $this->isd = 2205;
		$this->unofficial = true;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        $this->fighters = array("shuttles" => 24); 
       
        $this->turncost = 1.33;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 4;
        $this->gravitic = true;	
        
         
        $this->addPrimarySystem(new Reactor(9, 18, 0, 0));
        $this->addPrimarySystem(new CnC(9, 16, 0, 0));        
        $this->addPrimarySystem(new DualBurstBeam(5, 8, 5, 0, 360));
        $this->addPrimarySystem(new CargoBay(7, 12));

		$this->addFrontSystem(new TractorBeam(6, 4, 0, 0));
        $this->addFrontSystem(new Hangar(7, 9, 0));
        $this->addFrontSystem(new ImprovedBlastLaser(7, 10, 8, 300, 60));
        $this->addFrontSystem(new ImprovedBlastLaser(7, 10, 8, 300, 60));	
        $this->addFrontSystem(new EMWaveDisruptor(6, 8, 5, 240, 60));
        $this->addFrontSystem(new SurgeLaser(5, 0, 0, 240, 60));
        $this->addFrontSystem(new SurgeLaser(5, 0, 0, 300, 120));
        $this->addFrontSystem(new SurgeLaser(5, 0, 0, 300, 120));
        $this->addFrontSystem(new CargoBay(7, 15));
        $this->addFrontSystem(new Thruster(8, 8, 0, 5, 1));
        $this->addFrontSystem(new Thruster(8, 8, 0, 5, 1));	        
        
        $this->addAftSystem(new Engine(8, 13, 0, 10, 3)); 
        $this->addAftSystem(new Thruster(8, 6, 0, 4, 2));
        $this->addAftSystem(new Thruster(8, 8, 0, 6, 2));
        $this->addAftSystem(new DualBurstBeam(5, 8, 5, 90, 270));
        $this->addAftSystem(new SurgeLaser(5, 0, 0, 120, 300));
        $this->addAftSystem(new SurgeLaser(5, 0, 0, 120, 300));
        $this->addAftSystem(new EMWaveDisruptor(6, 8, 5, 0, 240));
        $this->addAftSystem(new CargoBay(7, 10));
        $this->addAftSystem(new Hangar(7, 7, 0));        

        $this->addLeftSystem(new Thruster(8, 13, 0, 5, 4));
		$this->addLeftSystem(new TractorBeam(6, 4, 0, 0));
        $this->addLeftSystem(new CargoBay(7, 15));
        $this->addLeftSystem(new Hangar(7, 9, 0));
        $this->addLeftSystem(new EMWaveDisruptor(6, 8, 5, 180, 60));
        $this->addLeftSystem(new HeavyBurstBeam(7, 9, 8, 240, 360)); 
	            
        $this->addRightSystem(new Thruster(8, 13, 0, 5, 3));
        $this->addRightSystem(new Scanner(8, 10, 5, 12));
        $this->addRightSystem(new CargoBay(7, 9));
        $this->addRightSystem(new CargoBay(7, 9));
        $this->addRightSystem(new CargoBay(7, 9));
        $this->addRightSystem(new SurgeLaser(5, 0, 0, 60, 180));
        $this->addRightSystem(new DualBurstBeam(5, 8, 5, 300, 180));
        $this->addRightSystem(new DualBurstBeam(5, 8, 5, 0, 240));	    
	    
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(8, 54));
        $this->addAftSystem(new Structure(8, 51));
        $this->addLeftSystem(new Structure(8, 60));
        $this->addRightSystem(new Structure(8, 60));
        $this->addPrimarySystem(new Structure(8, 40));
	    
        $this->hitChart = array(
            0 => array(
                11 => "Structure",
                12 => "Dual Burst Beam",
                14 => "Cargo Bay",
                17 => "Reactor", 
                20 => "C&C",
            ),
            1 => array(
                3 => "Thruster",
                4 => "Tractor Beam",
                6 => "Improved Blast Laser",
                8 => "Surge Laser",
                9 => "EM-Wave Disruptor",
                10 => "Hangar",
                12 => "Cargo Bay",
                18 => "Structure",
                20 => "Primary",
            ),
            2 => array(
                4 => "Thruster",
                6 => "Surge Laser",
                7 => "Dual Burst Beam",
                8 => "EM-Wave Disruptor",
                9 => "Hangar",
                10 => "Cargo Bay",
                12 => "Engine",
                18 => "Structure",
                20 => "Primary",
            ),
            3 => array(
                3 => "Thruster",
                4 => "Tractor Beam",
                6 => "Heavy Burst Beam",
                7 => "EM-Wave Disruptor",
                9 => "Hangar",
                11 => "Cargo Bay",
                18 => "Structure",
                20 => "Primary",
            ),
            4 => array(
                3 => "Thruster",
                5 => "Dual Burst Beam",
                6 => "Surge Laser",
                9 => "Cargo Bay",
                11 => "Scanner",
                18 => "Structure",
                20 => "Primary",
            ),
        );
    }
}
?>
