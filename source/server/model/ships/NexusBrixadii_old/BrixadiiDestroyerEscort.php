<?php
class BrixadiiDestroyerEscort extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 400;
        $this->faction = "Nexus Brixadii Clans (early)";
        $this->phpclass = "BrixadiiDestroyerEscort";
        $this->imagePath = "img/ships/Nexus/brixadii_battle_destroyer.png";
			$this->canvasSize = 125; //img has 200px per side
        $this->shipClass = "Destroyer Escort";
			$this->variantOf = "Battle Destroyer";
			$this->occurence = "common";
			$this->unofficial = true;
        $this->isd = 2057;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 13;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.50;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 40;
         
        $this->addPrimarySystem(new Reactor(4, 16, 0, 0));
        $this->addPrimarySystem(new CnC(5, 10, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 5, 5));
        $this->addPrimarySystem(new Engine(4, 14, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(1, 2));
        $this->addPrimarySystem(new Thruster(3, 7, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(3, 7, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(3, 7, 0, 3, 4));
        $this->addPrimarySystem(new Thruster(3, 7, 0, 3, 4));
      
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));
        $this->addFrontSystem(new NexusParticleBolter(3, 6, 2, 240, 60));
        $this->addFrontSystem(new NexusParticleBolter(3, 6, 2, 300, 120));
		$this->addFrontSystem(new NexusKineticBoxLauncher(0, 4, 0, 300, 60));
                
        $this->addAftSystem(new Thruster(3, 14, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 14, 0, 4, 2));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 120, 360));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 0, 240));
		$this->addAftSystem(new NexusChaffLauncher(2, 2, 1, 0, 360));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 50));
        $this->addAftSystem(new Structure( 3, 45));
        $this->addPrimarySystem(new Structure( 4, 40));

        $this->hitChart = array(
            0=> array(
                    7 => "Structure",
                    11 => "Thruster",
                    13 => "Scanner",
                    15 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    5 => "Thruster",
                    7 => "Light Particle Beam",
                    10 => "Particle Bolter",
					12 => "Kinetic Box Launcher",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
					7 => "Chaff Launcher",
					11 => "Light Particle Beam",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
