<?php
class CircasianTorminBeta extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 450;
        $this->faction = "ZEscalation Circasian Empire";
        $this->phpclass = "CircasianTorminBeta";
        $this->imagePath = "img/ships/EscalationWars/CircasianTormin.png";
			$this->canvasSize = 150; //img has 200px per side
        $this->shipClass = "Tormin Heavy Destroyer (1976 Refit)";
			$this->variantOf = "Tormin Heavy Destroyer";
			$this->occurence = "common";		
			$this->unofficial = true;
        $this->isd = 1976;
		$this->fighters = array("light"=>6);
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 0.50;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 6*5;
         
        $this->addPrimarySystem(new Reactor(4, 15, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 4, 6));
        $this->addPrimarySystem(new Engine(4, 13, 0, 8, 2));
        $this->addPrimarySystem(new Hangar(4, 8));
        $this->addPrimarySystem(new Thruster(2, 13, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(2, 13, 0, 4, 4));
      
        $this->addFrontSystem(new Thruster(2, 5, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 5, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));
		$this->addFrontSystem(new MediumPlasma(2, 5, 3, 240, 60));
		$this->addFrontSystem(new MediumPlasma(2, 5, 3, 300, 120));
		$this->addFrontSystem(new ParticleCannon(3, 8, 7, 300, 360));
		$this->addFrontSystem(new ParticleCannon(3, 8, 7, 0, 60));
		$this->addFrontSystem(new EWHeavyRocketLauncher(3, 6, 2, 300, 60));
                
        $this->addAftSystem(new Thruster(3, 7, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 7, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 7, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 7, 0, 2, 2));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 120, 300));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 60, 240));
        $this->addAftSystem(new MediumPlasma(2, 5, 3, 180, 300));
        $this->addAftSystem(new MediumPlasma(2, 5, 3, 60, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 46));
        $this->addAftSystem(new Structure( 4, 46));
        $this->addPrimarySystem(new Structure( 4, 40));
		
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
                    11 => "Thruster",
                    13 => "Scanner",
                    15 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
					6 => "Heavy Rocket Launcher",
                    8 => "Particle Cannon",
					10 => "Medium Plasma Cannon",
					12 => "Light Particle Beam",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
					8 => "Medium Plasma Cannon",
                    10 => "Light Particle Beam",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
