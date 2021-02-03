<?php
class CircasianGallahCarrier extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 185;
        $this->faction = "ZEscalation Circasian Empire";
        $this->phpclass = "CircasianGallahCarrier";
        $this->imagePath = "img/ships/EscalationWars/CircasianGallah.png";
			$this->canvasSize = 125; //img has 200px per side
        $this->shipClass = "Gallah Auxiliary Carrier";
			$this->unofficial = true;
        $this->isd = 1957;
		$this->fighters = array("light"=>18);		
        
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
        $this->addPrimarySystem(new Scanner(3, 6, 2, 4));
        $this->addPrimarySystem(new Engine(3, 8, 0, 5, 2));
        $this->addPrimarySystem(new Hangar(2, 3));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 3, 4));
      
        $this->addFrontSystem(new Thruster(2, 10, 0, 3, 1));
		$this->addFrontSystem(new Hangar(2, 6));
		$this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 180, 60));
		$this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 300, 180));
                
        $this->addAftSystem(new Thruster(2, 12, 0, 5, 2));
		$this->addAftSystem(new Hangar(2, 6));
		$this->addAftSystem(new Hangar(2, 6));
        $this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 180, 360));
        $this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 30));
        $this->addAftSystem(new Structure( 3, 30));
        $this->addPrimarySystem(new Structure( 3, 30));
		
        $this->hitChart = array(
            0=> array(
                    10 => "Structure",
                    13 => "Thruster",
                    14 => "Scanner",
                    16 => "Engine",
                    18 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    3 => "Thruster",
                    6 => "Light Particle Beam",
					10 => "Hangar",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    5 => "Thruster",
                    7 => "Light Particle Beam",
					11 => "Hangar",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
