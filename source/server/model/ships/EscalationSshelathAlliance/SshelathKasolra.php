<?php
class SshelathKasolra extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 475;
	$this->faction = "Escalation Wars Sshel'ath Alliance";
        $this->phpclass = "SshelathKasolra";
        $this->imagePath = "img/ships/EscalationWars/SshelathKasolra.png";
        $this->shipClass = "Kasolra Bombardment Cruiser";
        $this->shipSizeClass = 3;
		$this->canvasSize = 150; //img has 200px per side
		$this->unofficial = true;

		$this->isd = 1959;
        $this->limited = 33;
	
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 0.75;
        $this->turndelaycost = 1.0;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(48); //pass magazine capacity - class-SO launchers hold 12 rounds per mount
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 48); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
        
        $this->addPrimarySystem(new Reactor(4, 12, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 3, 5));
        $this->addPrimarySystem(new Engine(4, 11, 0, 6, 4));
		$this->addPrimarySystem(new Hangar(4, 3));
        $this->addPrimarySystem(new ReloadRack(4, 9)); 
  
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
		$this->addFrontSystem(new SoMissileRack(3, 6, 0, 240, 60));
		$this->addFrontSystem(new SoMissileRack(3, 6, 0, 240, 60));
        $this->addFrontSystem(new EWLightGaussCannon(2, 6, 3, 300, 60));
		$this->addFrontSystem(new SoMissileRack(3, 6, 0, 300, 120));
		$this->addFrontSystem(new SoMissileRack(3, 6, 0, 300, 120));

        $this->addAftSystem(new Thruster(3, 22, 0, 6, 2));
		$this->addAftSystem(new SoMissileRack(3, 6, 0, 120, 300));
		$this->addAftSystem(new SoMissileRack(3, 6, 0, 60, 240));

		$this->addLeftSystem(new LightParticleBeamShip(1, 2, 1, 180, 60));
		$this->addLeftSystem(new LightParticleBeamShip(1, 2, 1, 180, 60));
        $this->addLeftSystem(new EWLightGaussCannon(2, 6, 3, 180, 300));
        $this->addLeftSystem(new Thruster(3, 13, 0, 3, 3));

		$this->addRightSystem(new LightParticleBeamShip(1, 2, 1, 300, 180));
		$this->addRightSystem(new LightParticleBeamShip(1, 2, 1, 300, 180));
        $this->addRightSystem(new EWLightGaussCannon(2, 6, 3, 60, 180));
        $this->addRightSystem(new Thruster(3, 13, 0, 3, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(3, 46));
        $this->addAftSystem(new Structure(3, 32));
        $this->addLeftSystem(new Structure(3, 32));
        $this->addRightSystem(new Structure(3, 32));
        $this->addPrimarySystem(new Structure(4, 40));
		
		$this->hitChart = array(
			0=> array(
					10 => "Structure",
					12 => "Reload Rack",
					14 => "Scanner",
					16 => "Engine",
					18 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					8 => "Class-SO Missile Rack",
					9 => "Light Gauss Cannon",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					9 => "Class-SO Missile Rack",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					5 => "Thruster",
					8 => "Light Particle Beam",
					10 => "Light Gauss Cannon",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					8 => "Light Particle Beam",
					10 => "Light Gauss Cannon",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
