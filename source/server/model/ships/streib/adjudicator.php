<?php
class Adjudicator extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 1250;
	$this->faction = "Streib";
        $this->phpclass = "Adjudicator";
        $this->imagePath = "img/ships/collector.png";
        $this->shipClass = "Adjudicator";
        $this->shipSizeClass = 3;
        
        $this->isd = 2205;
		$this->unofficial = true;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 16;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->gravitic = true;	
        
         
        $this->addPrimarySystem(new Reactor(9, 18, 0, 0));
        $this->addPrimarySystem(new TractorBeam(7, 4, 0, 0));
        $this->addPrimarySystem(new Hangar(7, 2, 0));
        
        $this->addFrontSystem(new Thruster(9, 7, 0, 6, 1));	
        $this->addFrontSystem(new ImprovedBlastLaser(7, 10, 8, 300, 60));
        $this->addFrontSystem(new ImprovedBlastLaser(7, 10, 8, 300, 60));	
        $this->addFrontSystem(new MediumBurstBeam(6, 7, 6, 300, 60));
        $this->addFrontSystem(new MediumBurstBeam(6, 7, 6, 300, 60));
        $this->addLeftSystem(new EMWaveDisruptor(6, 8, 5, 180, 60));
        
        
        $this->addAftSystem(new Engine(9, 11, 0, 9, 2)); 
        $this->addAftSystem(new JumpEngine(9, 10, 4, 10));
        $this->addAftSystem(new Thruster(9, 3, 0, 2, 2));
        $this->addAftSystem(new Thruster(9, 8, 0, 7, 2));
        $this->addAftSystem(new ImprovedBlastLaser(7, 10, 8, 120, 240));
        $this->addAftSystem(new DualBurstBeam(6, 8, 5, 90, 270));
        
        $this->addLeftSystem(new CnC(9, 12, 0, 0));
        $this->addLeftSystem(new Thruster(8, 10, 0, 5, 4));
        $this->addLeftSystem(new ImprovedBlastLaser(7, 10, 8, 240, 0));
        $this->addLeftSystem(new BurstPulseCannon(6, 6, 7, 180, 360));
        $this->addLeftSystem(new SurgeLaser(6, 0, 0, 120, 360));
	            
        $this->addRightSystem(new Thruster(8, 10, 0, 5, 3));
        $this->addRightSystem(new Scanner(8, 8, 5, 12));
        $this->addRightSystem(new ImprovedBlastLaser(7, 10, 8, 120, 240));
        $this->addRightSystem(new SurgeLaser(6, 0, 0, 300, 120));
        $this->addRightSystem(new SurgeLaser(6, 0, 0, 60, 240));
        $this->addRightSystem(new EMWaveDisruptor(6, 8, 5, 0, 180));
	    
	    
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(9, 40));
        $this->addAftSystem(new Structure(9, 33));
        $this->addLeftSystem(new Structure(9, 40));
        $this->addRightSystem(new Structure(9, 40));
        $this->addPrimarySystem(new Structure(9, 40));
	    
        $this->hitChart = array(
            0 => array(
                13 => "Structure",
                15 => "Tractor Beam",
                17 => "Hangar",
                20 => "Reactor",
            ),
            1 => array(
                3 => "Thruster",
                7 => "Improved Blast Laser",
                9 => "Medium Burst Beam",
                10 => "EM-Wave Disruptor",
                18 => "Structure",
                20 => "Primary",
            ),
            2 => array(
                5 => "Thruster",
                7 => "Improved Blast Laser",
                8 => "Dual Burst Beam",
                10 => "Engine",
                12 => "Jump Engine",
                18 => "Structure",
                20 => "Primary",
            ),
            3 => array(
                3 => "Thruster",
                5 => "Improved Blast Laser",
                7 => "Burst Pulse Cannon",
                8 => "Surge Laser",
                9 => "C&C",
                18 => "Structure",
                20 => "Primary",
            ),
            4 => array(
                3 => "Thruster",
                5 => "Improved Blast Laser",
                7 => "Surge Laser",
                9 => "EM-Wave Disruptor",
                10 => "Scanner",
                18 => "Structure",
                20 => "Primary",
            ),
        );
    }
}
?>
