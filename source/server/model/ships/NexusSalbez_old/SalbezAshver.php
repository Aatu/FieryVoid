<?php
class SalbezAshver extends BaseShipNoAft{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 480;
		$this->faction = "Nexus Sal-bez Coalition (early)";
        $this->phpclass = "SalbezAshver";
        $this->imagePath = "img/ships/Nexus/salbez_felriz3.png";
			$this->canvasSize = 120; //img has 200px per side
        $this->shipClass = "Ash-ver Shock Cruiser";
		$this->unofficial = true;
        $this->limited = 33;
		$this->isd = 2082;
         
        $this->fighters = array("heavy"=>6);

        $this->forwardDefense = 15;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 1.0;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
		$this->iniativebonus = 0;
         
        $this->addPrimarySystem(new Reactor(3, 16, 0, 0));
        $this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 16, 4, 6));
        $this->addPrimarySystem(new Engine(3, 18, 0, 8, 3));
		$this->addPrimarySystem(new Hangar(1, 8));
		$this->addPrimarySystem(new Thruster(3, 20, 0, 8, 2));

        $this->addFrontSystem(new Thruster(2, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 10, 0, 3, 1));
		$this->addFrontSystem(new NexusBoltTorpedo(2, 5, 2, 300, 60));
		$this->addFrontSystem(new NexusBoltTorpedo(2, 5, 2, 300, 60));
        
		$this->addLeftSystem(new Thruster(3, 14, 0, 5, 3));
		$this->addLeftSystem(new NexusLightMagGun(3, 7, 6, 240, 360));
		$this->addLeftSystem(new LightParticleBeamShip(1, 2, 1, 240, 60));
		$this->addLeftSystem(new LightParticleBeamShip(1, 2, 1, 180, 360));
		$this->addLeftSystem(new LightParticleBeamShip(1, 2, 1, 180, 360));
		$this->addLeftSystem(new LightParticleBeamShip(1, 2, 1, 120, 300));

		$this->addRightSystem(new Thruster(3, 14, 0, 5, 4));
		$this->addRightSystem(new NexusLightMagGun(3, 7, 6, 0, 120));
		$this->addRightSystem(new LightParticleBeamShip(1, 2, 1, 300, 120));
		$this->addRightSystem(new LightParticleBeamShip(1, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(1, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(1, 2, 1, 60, 240));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 36));
        $this->addLeftSystem(new Structure( 4, 36));
        $this->addRightSystem(new Structure( 4, 36));
        $this->addPrimarySystem(new Structure( 4, 36));
		
        $this->hitChart = array(
            0=> array(
                    9 => "Structure",
                    11 => "Thruster",
					13 => "Scanner",
                    15 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    6 => "Thruster",
					8 => "Bolt Torpedo",
					18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    5 => "Thruster",
					7 => "Light Mag Gun",
					10 => "Light Particle Beam",
                    18 => "Structure",
                    20 => "Primary",
			),
            4=> array(
                    5 => "Thruster",
					7 => "Light Mag Gun",
					10 => "Light Particle Beam",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );

		
    }
}
?>