<?php
class BrixadiiStrategicTransport extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 150;
        $this->faction = "Nexus Support Units";
        $this->phpclass = "BrixadiiStrategicTransport";
        $this->imagePath = "img/ships/Nexus/brixadii_battle_destroyer.png";
			$this->canvasSize = 125; //img has 200px per side
        $this->shipClass = "Brixadii Marrgex Strategic Transport";
		$this->unofficial = true;
        $this->isd = 2063;
        $this->limited = 10;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 999;
        $this->pivotcost = 999;
        $this->iniativebonus = 0;
         
        $this->addPrimarySystem(new Reactor(3, 10, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 9, 4, 4));
        $this->addPrimarySystem(new Engine(3, 25, 0, 10, 3));
        $this->addPrimarySystem(new Thruster(3, 23, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(3, 23, 0, 5, 4));
      
        $this->addFrontSystem(new Thruster(3, 15, 0, 5, 1));
        $this->addFrontSystem(new Thruster(3, 15, 0, 5, 1));
    	$this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 240, 60));
        $this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 300, 120));
        $this->addFrontSystem(new CargoBay(2, 40));
        $this->addFrontSystem(new CargoBay(2, 40));
                
        $this->addAftSystem(new Thruster(3, 20, 0, 5, 2));
        $this->addAftSystem(new Thruster(3, 20, 0, 5, 2));
        $this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 90, 270));
        $this->addAftSystem(new Hangar(1, 2, 2));
        $this->addAftSystem(new CargoBay(2, 40));
        $this->addAftSystem(new CargoBay(2, 40));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 36));
        $this->addAftSystem(new Structure( 3, 31));
        $this->addPrimarySystem(new Structure( 3, 40));
        
        $this->hitChart = array(
            0=> array(
                    10 => "Structure",
                    13 => "Thruster",
                    15 => "Scanner",
                    18 => "Engine",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    6 => "Thruster",
                    7 => "Light Particle Beam",
					12 => "Cargo Bay",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
					7 => "Light Particle Beam",
					8 => "Hangar",
					13 => "Cargo Bay",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
