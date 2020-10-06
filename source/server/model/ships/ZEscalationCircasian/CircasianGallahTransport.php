<?php
class CircasianGallahTransport extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 150;
        $this->faction = "ZEscalation Circasian";
        $this->phpclass = "CircasianGallahTransport";
        $this->imagePath = "img/ships/EscalationWars/CircasianGallah.png";
			$this->canvasSize = 125; //img has 200px per side
        $this->shipClass = "Gallah Transport";
			$this->variantOf = "Gallah Auxiliary Carrier";
			$this->occurence = "common";
			$this->unofficial = true;
        $this->isd = 1942;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 14;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 6*5;
        
         
        $this->addPrimarySystem(new Reactor(3, 7, 0, 0));
        $this->addPrimarySystem(new CnC(3, 5, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 6, 2, 3));
        $this->addPrimarySystem(new Engine(2, 8, 0, 4, 2));
        $this->addPrimarySystem(new Hangar(2, 3));
		$this->addPrimarySystem(new CargoBay(1, 30));
		$this->addPrimarySystem(new CargoBay(1, 30));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 3, 4));
      
        $this->addFrontSystem(new Thruster(2, 10, 0, 3, 1));
		$this->addFrontSystem(new CargoBay(2, 30));
		$this->addFrontSystem(new LightParticleBeamShip(0, 2, 1, 180, 60));
		$this->addFrontSystem(new LightParticleBeamShip(0, 2, 1, 300, 180));
                
        $this->addAftSystem(new Thruster(1, 12, 0, 4, 2));
        $this->addAftSystem(new LightParticleBeamShip(0, 2, 1, 180, 360));
        $this->addAftSystem(new LightParticleBeamShip(0, 2, 1, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 30));
        $this->addAftSystem(new Structure( 3, 30));
        $this->addPrimarySystem(new Structure( 3, 30));
		
        $this->hitChart = array(
            0=> array(
                    6 => "Structure",
                    9 => "Thruster",
					13 => "Cargo",
                    14 => "Scanner",
                    16 => "Engine",
                    18 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    3 => "Thruster",
                    5 => "Light Particle Beam",
					10 => "Cargo",
					17 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    5 => "Thruster",
                    8 => "Light Particle Beam",
                    17 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
