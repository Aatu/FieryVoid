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
		
		$this->unofficial = true;
        
         
        $this->addPrimarySystem(new Reactor(9, 16, 0, 0));
        $this->addPrimarySystem(new Engine(9, 12, 0, 10, 2));
        $this->addPrimarySystem(new TractorBeam(8, 4, 0, 0));

        $this->addFrontSystem(new Thruster(8, 6, 0, 5, 1));
        $this->addFrontSystem(new Thruster(8, 6, 0, 5, 1));
        $this->addFrontSystem(new MediumBurstBeam(6, 7, 6, 240, 60));
        $this->addFrontSystem(new BurstPulseCannon(7, 6, 6, 300, 60));
        $this->addFrontSystem(new MediumBurstBeam(6, 7, 6, 300, 120));

        $this->addAftSystem(new Thruster(9, 5, 0, 3, 2)); //reduced size to match engine thrust
        $this->addAftSystem(new Thruster(9, 8, 0, 7, 2));
        $this->addAftSystem(new MediumBurstBeam(6, 7, 6, 60, 240));
        $this->addAftSystem(new ImprovedBlastLaser(7, 10, 8, 120, 240));
        $this->addAftSystem(new MediumBurstBeam(6, 7, 6, 120, 300));

        $this->addLeftSystem(new Thruster(8, 10, 0, 5, 4));//changed 3-13 to more Streib-like 8-10
        $this->addLeftSystem(new JumpEngine(9, 10, 4, 10));
        $this->addLeftSystem(new CargoBay(9, 9));
        $this->addLeftSystem(new ImprovedBlastLaser(7, 10, 8, 300, 60));
        $this->addLeftSystem(new HeavyBurstBeam(7, 9, 8, 300, 60));
        $this->addLeftSystem(new DualBurstBeam(5, 8, 5, 180, 60));
        $this->addLeftSystem(new EMWaveDisruptor(6, 8, 5, 180, 60));

        $this->addRightSystem(new Thruster(8, 10, 0, 5, 3));  //changed 3-13 to more Streib-like 8-10
        $this->addRightSystem(new Hangar(8, 6, 0));
        $this->addRightSystem(new CnC(9, 12, 0, 0));
        $this->addRightSystem(new Scanner(9, 8, 5, 12));
        $this->addRightSystem(new CargoBay(9, 9));
        $this->addRightSystem(new DualBurstBeam(5, 8, 5, 300, 180));
        $this->addRightSystem(new EMWaveDisruptor(6, 8, 4, 300, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(9, 40));
        $this->addAftSystem(new Structure(9, 33));
        $this->addLeftSystem(new Structure(9, 40));
        $this->addRightSystem(new Structure(9, 40));
        $this->addPrimarySystem(new Structure(9, 40));


        $this->hitChart = array(
            0 => array(
                12 => "Structure",
                14 => "Tractor Beam",
                17 => "Engine",
                20 => "Reactor",
            ),
            1 => array(
                3 => "Thruster",
                7 => "Medium Burst Beam",
                9 => "Burst Pulse Cannon",
                18 => "Structure",
                20 => "Primary",
            ),
            2 => array(
                6 => "Thruster",
                8 => "Improved Blast Laser",
                10 => "Medium Burst Beam",
                18 => "Structure",
                20 => "Primary",
            ),
            3 => array(
                3 => "Thruster",
                5 => "Improved Blast Laser",
                7 => "Heavy Burst Beam",
                9 => "Dual Burst Beam",
                10 => "EM-Wave Disruptor",
                11 => "Cargo Bay",
                13 => "Jump Engine",
                18 => "Structure",
                20 => "Primary",
            ),
            4 => array(
                3 => "Thruster",
                5 => "Dual Burst Beam",
                7 => "EM-Wave Disruptor",
                9 => "Scanner",
                10 => "C&C",
                11 => "Hangar",
                12 => "Cargo Bay",
                18 => "Structure",
                20 => "Primary",
            ),
        );
    }
}
?>
