<?php
class AlacanAzan extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 425;
	$this->faction = "Small Races";
        $this->phpclass = "AlacanAzan";
        $this->imagePath = "img/ships/AlacanAzafac.png";
        $this->shipClass = "Alacan Azan Auxiliary Cruiser";
		$this->variantOf = "Alacan Azafac Armed Freighter";
		$this->occurence = "common";
		$this->unofficial = true;
		$this->fighters = array("light"=>12);		
		$this->isd = 2224;
        
        $this->forwardDefense = 18;
        $this->sideDefense = 18;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 999;
        $this->pivotcost = 999;
        $this->iniativebonus = 5;
        
        $this->addPrimarySystem(new Reactor(5, 15, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 8, 6, 5));
        $this->addPrimarySystem(new Engine(4, 9, 0, 6, 3));
        $this->addPrimarySystem(new Thruster(4, 12, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(4, 12, 0, 4, 4));
        $this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 240, 360));
        $this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 120));
   
        $this->addFrontSystem(new Thruster(4, 6, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 6, 0, 3, 1));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 360));
        $this->addFrontSystem(new CustomLightSoMissileRack(3, 6, 0, 300, 60));
        $this->addFrontSystem(new LaserCutter(4, 6, 4, 240, 360));
        $this->addFrontSystem(new LaserCutter(4, 6, 4, 0, 120));
        $this->addFrontSystem(new CustomLightSoMissileRack(3, 6, 0, 300, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 0, 120));

        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 180, 300));
		$this->addAftSystem(new Hangar(3,5));
		$this->addAftSystem(new Hangar(3,3));
		$this->addAftSystem(new Hangar(3,5));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 60, 180));
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));


        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 32));
        $this->addAftSystem(new Structure(3, 32));
        $this->addPrimarySystem(new Structure(4, 36));
		
		$this->hitChart = array(
			0=> array(
					7 => "Structure",
					10 => "Thruster",
					12 => "Light Particle Beam",
					14 => "Scanner",
					16 => "Engine",
					18 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					3 => "Thruster",
					6 => "SO-Missile Rack",
					7 => "Light Particle Beam",
					10 => "Laser Cutter",
					17 => "Structure",
					20 => "Primary",
			),
			2=> array(
					4 => "Thruster",
					6 => "Light Particle Beam",
					11 => "Hangar",
					17 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
