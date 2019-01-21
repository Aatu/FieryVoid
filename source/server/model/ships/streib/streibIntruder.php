<?php
class StreibIntruder extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 430;
		$this->faction = "Streib";
        $this->phpclass = "StreibIntruder";
        $this->imagePath = "img/ships/intruder.png";
        $this->shipClass = "Intruder";
        $this->canvasSize = 100;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 13;
        $this->fighters = array("shuttles" => 2);
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->gravitic = true;	
		$this->unofficial = true;
        
         
        $this->addPrimarySystem(new Reactor(8, 10, 0, 0));
        $this->addPrimarySystem(new Scanner(7, 6, 4, 9));
        $this->addPrimarySystem(new JumpEngine(7, 8, 4, 12));
        $this->addPrimarySystem(new SurgeLaser(5, 6, 3, 0, 240));
        $this->addPrimarySystem(new Hangar(6, 2, 0));
        $this->addPrimarySystem(new CargoBay(6, 9));
        $this->addPrimarySystem(new CargoBay(7, 6));
        $this->addPrimarySystem(new Thruster(7, 8, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(7, 8, 0, 4, 4));

        $this->addFrontSystem(new Thruster(7, 5, 0, 4, 1));
        $this->addFrontSystem(new Thruster(7, 3, 0, 2, 1));
        $this->addFrontSystem(new TractorBeam(6, 4, 0, 0));
        $this->addFrontSystem(new SurgeLaser(5, 0, 0, 240, 60));
        $this->addFrontSystem(new SurgeLaser(5, 0, 0, 300, 120));
        $this->addFrontSystem(new EMWaveDisruptor(5, 8, 5, 300, 180));
        $this->addFrontSystem(new CnC(8, 9, 0, 0));

        $this->addAftSystem(new Engine(7, 10, 0, 9, 2));
        $this->addAftSystem(new Thruster(8, 10, 0, 8, 2));
        $this->addAftSystem(new SurgeLaser(5, 6, 3, 180, 360));
  
        $this->addPrimarySystem(new Structure(8, 46));
		
        $this->hitChart = array(
            0 => array(
                6 => "Thruster",
                8 => "Surge Laser",
                12 => "Cargo Bay",
                15 => "Jump Engine",
                16 => "Hangar",
                18 => "Scanner",
                20 => "Reactor",
            ),
            1 => array(
                3 => "Thruster",
                6 => "Surge Laser",
                8 => "EM-Wave Disruptor",
                9 => "Tractor Beam",
                10 => "C&C",
                17 => "Structure",
                20 => "Primary",
            ),
            2 => array(
                5 => "Thruster",
                6 => "Surge Laser",
                8 => "Engine",
                17 => "Structure",
                20 => "Primary",
            ),
        );
    }
}
?>
