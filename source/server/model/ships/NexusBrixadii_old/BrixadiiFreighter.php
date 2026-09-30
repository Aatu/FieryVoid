<?php
class BrixadiiFreighter extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 100;
        $this->faction = "Nexus Support Units";
        $this->phpclass = "BrixadiiFreighter";
        $this->imagePath = "img/ships/Nexus/brixadii_strategic_transport.png";
		$this->canvasSize = 100; //img has 200px per side
        $this->shipClass = "Brixadii Vreltix Civilian Freighter";
		$this->unofficial = true;
   		$this->isd = 1941;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 14;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 2;
        $this->rollcost = 999;
        $this->pivotcost = 999;
        $this->iniativebonus = -2*5;
         
        $this->addPrimarySystem(new Reactor(2, 3, 0, 0));
        $this->addPrimarySystem(new CnC(2, 3, 0, 0));
        $this->addPrimarySystem(new Scanner(2, 9, 2, 2));
        $this->addPrimarySystem(new Engine(2, 8, 0, 6, 4));
        $this->addPrimarySystem(new Hangar(0, 2, 2));
        $this->addPrimarySystem(new Thruster(2, 12, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(2, 12, 0, 3, 4));
      
        $this->addFrontSystem(new Thruster(2, 12, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 12, 0, 3, 1));
		$this->addFrontSystem(new LightParticleProjector(2, 3, 1, 180, 60));
		$this->addFrontSystem(new LightParticleProjector(2, 3, 1, 300, 180));
        $this->addFrontSystem(new CargoBay(2, 40));
        $this->addFrontSystem(new CargoBay(2, 40));
                
        $this->addAftSystem(new Thruster(2, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 12, 0, 3, 2));
        $this->addAftSystem(new CargoBay(2, 40));
        $this->addAftSystem(new CargoBay(2, 40));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure( 3, 40));
        $this->hitChart = array(
            0=> array(
                    9 => "Thruster",
					11 => "Hangar",
                    14 => "Scanner",
                    17 => "Engine",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    3 => "Thruster",
                    4 => "Light Particle Projector",
					12 => "Cargo Bay",
					17 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    4 => "Thruster",
					12 => "Cargo Bay",
                    17 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}

?>
