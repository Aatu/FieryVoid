<?php
class AlacanAzafac extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 340;
	$this->faction = "Small Races";
        $this->phpclass = "AlacanAzafac";
        $this->imagePath = "img/ships/AbbaiSellac.png";
        $this->shipClass = "Alacan Azafac Armed Freighter";
		$this->isd = 2208;
        
        $this->forwardDefense = 18;
        $this->sideDefense = 18;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 999;
        $this->pivotcost = 999;
        $this->iniativebonus = -20;
        
        $this->addPrimarySystem(new Reactor(3, 10, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 2, 4));
        $this->addPrimarySystem(new Engine(3, 9, 0, 6, 4));
        $this->addPrimarySystem(new Thruster(3, 12, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 12, 0, 4, 4));
		$this->addPrimarySystem(new CargoBay(3, 28));
		$this->addPrimarySystem(new CargoBay(3, 28));
        $this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 240, 360));
        $this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 120));
   
        $this->addFrontSystem(new Thruster(3, 6, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 6, 0, 3, 1));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 360));
        $this->addFrontSystem(new SoMissileRack(3, 6, 0, 300, 60));
		$this->addFrontSystem(new CargoBay(3, 28));
		$this->addFrontSystem(new CargoBay(3, 28));
        $this->addFrontSystem(new SoMissileRack(3, 6, 0, 300, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 0, 120));

        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
		$this->addAftSystem(new CargoBay(3, 28));
		$this->addAftSystem(new Hangar(3,3));
		$this->addAftSystem(new CargoBay(3, 28));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));


        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(3, 32));
        $this->addAftSystem(new Structure(3, 32));
        $this->addPrimarySystem(new Structure(3, 36));
		
		$this->hitChart = array(
			0=> array(
					5 => "Structure",
					7 => "Thruster",
					8 => "Light Particle Beam",
					12 => "Cargo Bay",
					14 => "Scanner",
					16 => "Engine",
					18 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					3 => "Thruster",
					5 => "SO-Missile Rack",
					6 => "Light Particle Beam",
					10 => "Cargo Bay",
					17 => "Structure",
					20 => "Primary",
			),
			2=> array(
					4 => "Thruster",
					6 => "Light Particle Beam",
					10 => "Cargo Bay",
					11 => "Hangar",
					17 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
