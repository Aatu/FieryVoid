<?php
class SalbezUshkrit extends BaseShipNoAft{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 490;
		$this->faction = "ZNexus Sal-bez";
        $this->phpclass = "SalbezUshkrit";
        $this->imagePath = "img/ships/Nexus/salbez_ushkrit.png";
			$this->canvasSize = 155; //img has 200px per side
        $this->shipClass = "Ush-k'rit Command Cruiser";
			$this->variantOf = "Fel-riz Patrol Cruiser";
			$this->occurence = "rare";
		$this->unofficial = true;
		$this->isd = 2063;
         
        $this->fighters = array("heavy"=>6);

        $this->forwardDefense = 15;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 1.0;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
		$this->iniativebonus = 0;
         
        $this->addPrimarySystem(new Reactor(3, 22, 0, 0));
        $this->addPrimarySystem(new CnC(4, 24, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 16, 4, 5));
        $this->addPrimarySystem(new Engine(3, 18, 0, 7, 3));
		$this->addPrimarySystem(new Hangar(1, 8));
		$this->addPrimarySystem(new Thruster(3, 20, 0, 7, 2));
		$this->addPrimarySystem(new JumpEngine(4, 12, 4, 36));

        $this->addFrontSystem(new Thruster(2, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 10, 0, 3, 1));
		$this->addFrontSystem(new LightLaser(2, 4, 3, 240, 360));
		$this->addFrontSystem(new NexusHeavyLaserCutter(2, 8, 5, 330, 30));
		$this->addFrontSystem(new NexusBoltTorpedo(2, 5, 2, 300, 60));
		$this->addFrontSystem(new LightLaser(2, 4, 3, 0, 120));
        
		$this->addLeftSystem(new Thruster(3, 14, 0, 4, 3));
		$this->addLeftSystem(new LaserCutter(3, 6, 4, 300, 360));
		$this->addLeftSystem(new LightParticleBeamShip(1, 2, 1, 240, 60));
		$this->addLeftSystem(new LightParticleBeamShip(1, 2, 1, 180, 360));
		$this->addLeftSystem(new LightParticleBeamShip(1, 2, 1, 120, 300));
		$this->addLeftSystem(new LightLaser(2, 4, 3, 120, 300));

		$this->addRightSystem(new Thruster(3, 14, 0, 4, 4));
		$this->addRightSystem(new LaserCutter(3, 6, 4, 0, 60));
		$this->addRightSystem(new LightParticleBeamShip(1, 2, 1, 300, 120));
		$this->addRightSystem(new LightParticleBeamShip(1, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(1, 2, 1, 60, 240));
		$this->addRightSystem(new LightLaser(2, 4, 3, 60, 240));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 36));
        $this->addLeftSystem(new Structure( 4, 36));
        $this->addRightSystem(new Structure( 4, 36));
        $this->addPrimarySystem(new Structure( 4, 36));
		
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
					10 => "Jump Engine",
                    12 => "Thruster",
					14 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    6 => "Thruster",
					8 => "Heavy Laser Cutter",
					10 => "Light Laser",
					11 => "Bolt Torpedo",
					18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    5 => "Thruster",
					7 => "Laser Cutter",
					9 => "Light Particle Beam",
					11 => "Light Laser",
                    18 => "Structure",
                    20 => "Primary",
			),
            4=> array(
                    5 => "Thruster",
					7 => "Laser Cutter",
					9 => "Light Particle Beam",
					11 => "Light Laser",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );

		
    }
}
?>