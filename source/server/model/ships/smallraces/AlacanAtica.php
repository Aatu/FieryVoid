<?php
class AlacanAtica extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 600;
	$this->faction = "Small Races";
        $this->phpclass = "AlacanAtica";
        $this->imagePath = "img/ships/AlacanAtrimis.png";
        $this->shipClass = "Alacan Atica Command Cruiser";
		$this->variantOf = "Alacan Atrimis Cruiser";
		$this->occurence = "unique";
		//$this->limited = 10;
        $this->shipSizeClass = 3;
		$this->fighters = array("light"=>24);

		$this->isd = 2228;
        
        $this->forwardDefense = 16;
        $this->sideDefense = 16;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(5, 15, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 12, 4, 6));
        $this->addPrimarySystem(new Engine(5, 14, 0, 8, 3));
   
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new CombatLaser(3, 7, 7, 240, 0));
        $this->addFrontSystem(new CombatLaser(3, 7, 7, 300, 60));
        $this->addFrontSystem(new CombatLaser(3, 7, 7, 0, 120));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));

        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));
        $this->addAftSystem(new Hangar(3, 26));
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));
        $this->addAftSystem(new SMissileRack(3, 6, 0, 60, 240));
        $this->addAftSystem(new SMissileRack(3, 6, 0, 120, 300));

        $this->addLeftSystem(new SMissileRack(3, 6, 0, 240, 60));
        $this->addLeftSystem(new Thruster(3, 13, 0, 4, 3));

        $this->addRightSystem(new SMissileRack(3, 6, 0, 300, 120));
        $this->addRightSystem(new Thruster(3, 13, 0, 4, 4));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 39));
        $this->addAftSystem(new Structure(4, 39));
        $this->addLeftSystem(new Structure(4, 40));
        $this->addRightSystem(new Structure(4, 40));
        $this->addPrimarySystem(new Structure(5, 28));
		
		$this->hitChart = array(
			0=> array(
					10 => "Structure",
					13 => "Scanner",
					16 => "Engine",
					18 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					7 => "Combat Laser",
					9 => "Light Particle Beam",
					17 => "Structure",
					20 => "Primary",
			),
			2=> array(
					4 => "Thruster",
					6 => "S-Missile Rack",	
					8 => "Light Particle Beam",
					11 => "Hangar",
					17 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					6 => "S-Missile Rack",
					17 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					6 => "S-Missile Rack",
					17 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
