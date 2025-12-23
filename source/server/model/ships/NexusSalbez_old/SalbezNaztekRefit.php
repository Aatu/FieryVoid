<?php
class SalbezNaztekRefit extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 110;
        $this->faction = "Nexus Support Units";
        $this->phpclass = "SalbezNaztekRefit";
        $this->imagePath = "img/ships/Nexus/salbez_zefjem3.png";
			$this->canvasSize = 115; //img has 200px per side
        $this->shipClass = "Sal-bez Naz-tek Heavy Freighter (2092)";
			$this->variantOf = "Sal-bez Naz-tek Heavy Freighter";
			$this->occurence = "common";
		$this->unofficial = true;
        $this->isd = 2092;
		
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 30;
         
        $this->addPrimarySystem(new Reactor(3, 8, 0, 0));
        $this->addPrimarySystem(new CnC(3, 7, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 2, 3));
        $this->addPrimarySystem(new Engine(3, 11, 0, 6, 3));
        $this->addPrimarySystem(new Hangar(1, 2));
        $this->addPrimarySystem(new Thruster(3, 14, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(3, 14, 0, 3, 4));
      
        $this->addFrontSystem(new Thruster(2, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 10, 0, 3, 1));
        $this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 270, 90));
        $this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 270, 90));
        $this->addFrontSystem(new Hangar(2, 4));
		$this->addFrontSystem(new CargoBay(2, 24));
                
        $this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
 		$this->addAftSystem(new CargoBay(2, 42));
        $this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 120, 300));
        $this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 60, 240));
 		$this->addAftSystem(new CargoBay(2, 42));
       
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 33));
        $this->addAftSystem(new Structure( 2, 33));
        $this->addPrimarySystem(new Structure( 3, 32));
		
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
                    11 => "Thruster",
                    13 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
                    7 => "Cargo Bay",
                    9 => "Light Particle Beam",
					10 => "Hangar",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    5 => "Thruster",
                    7 => "Light Particle Beam",
					12 => "Cargo Bay",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
