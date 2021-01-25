<?php
class SshelathKaraut1986 extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 485;
        $this->faction = "ZEscalation Sshelath";
        $this->phpclass = "SshelathKaraut1986";
        $this->imagePath = "img/ships/EscalationWars/SshelathKaraut.png";
			$this->canvasSize = 125; //img has 200px per side
        $this->shipClass = "Karaut Stealth Destroyer (1986 Refit)";
			$this->variantOf = "Karaut Stealth Destroyer";
			$this->occurence = "common";		
			$this->unofficial = true;
			$this->limited = 33;
        $this->isd = 1986;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 6*5;
        
        $this->addPrimarySystem(new Reactor(4, 15, 0, -5));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 14, 4, 6));
        $this->addPrimarySystem(new Engine(4, 11, 0, 9, 3));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 5, 4));
      
        $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));
		$this->addFrontSystem(new GaussCannon(3, 10, 4, 300, 60));
		$this->addFrontSystem(new GaussCannon(3, 10, 4, 300, 60));
		$this->addFrontSystem(new LaserCutter(2, 6, 4, 240, 360));
		$this->addFrontSystem(new LaserCutter(2, 6, 4, 0, 120));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 120));
                
        $this->addAftSystem(new Thruster(3, 16, 0, 9, 2));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addAftSystem(new Jammer(4, 8, 5));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 44));
        $this->addAftSystem(new Structure( 4, 48));
        $this->addPrimarySystem(new Structure( 5, 48));
		
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
                    10 => "Thruster",
                    13 => "Scanner",
                    15 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    3 => "Thruster",
                    5 => "Gauss Cannon",
					8 => "Laser Cutter",
					9 => "Light Particle Beam",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    5 => "Thruster",
                    7 => "Jammer",
					11 => "Light Particle Beam",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
