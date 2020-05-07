<?php
class Motenai extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 625;
	$this->faction = "Abbai (WotCR)";
        $this->phpclass = "Motenaion";
        $this->imagePath = "img/ships/AbbaiMotenai.png";
        $this->shipClass = "Motenai Missile Cruiser";
        $this->shipSizeClass = 3;
		
	    $this->unofficial = true;
		$this->notes = "official Motenai Mine Layer with Class-SO racks replacing Mine Launchers";

        $this->limited = 33;
        $this->isd = 2025;
        
        $this->forwardDefense = 18;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(4, 17, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 5, 6));  //+4 Minesweeper
        $this->addPrimarySystem(new Engine(4, 16, 0, 8, 4));
 	$this->addPrimarySystem(new Hangar(4, 8));
        $this->addPrimarySystem(new ShieldGenerator(5, 16, 4, 4));
   
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 1, 0, 60));
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 1, 300, 360));
        $this->addFrontSystem(new AssaultLaser(3, 6, 4, 300, 60));
        $this->addFrontSystem(new SoMissileRack(3, 6, 0, 300, 60));
        $this->addFrontSystem(new SoMissileRack(3, 6, 0, 300, 60));
        $this->addFrontSystem(new SoMissileRack(3, 6, 0, 300, 60));
        $this->addFrontSystem(new SoMissileRack(3, 6, 0, 300, 60));

        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 1, 180, 240));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 1, 120, 180));
        $this->addAftSystem(new JumpEngine(5, 12, 4, 32));

        $this->addLeftSystem(new SMissileRack(3, 6, 0, 240, 360));
        $this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        $this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        $this->addLeftSystem(new GraviticShield(0, 6, 0, 1, 240, 300));
        $this->addLeftSystem(new Thruster(3, 15, 0, 5, 3));

        $this->addRightSystem(new SoMissileRack(3, 6, 0, 0, 120));
        $this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        $this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        $this->addRightSystem(new GraviticShield(0, 6, 0, 1, 60, 120));
        $this->addRightSystem(new Thruster(3, 15, 0, 5, 4));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 36));
        $this->addAftSystem(new Structure(4, 36));
        $this->addLeftSystem(new Structure(4, 44));
        $this->addRightSystem(new Structure(4, 44));
        $this->addPrimarySystem(new Structure(4, 36));
		
		$this->hitChart = array(
			0=> array(
					7 => "Structure",
					9 => "Scanner",
					12 => "Shield Generator",
					15 => "Hangar",
					16 => "Engine",
					18 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					6 => "Gravitic Shield",	
					7 => "Assault Laser",
                        		10 => "Class-S Missile Rack",
					17 => "Structure",
					20 => "Primary",
			),
			2=> array(
					5 => "Thruster",
					7 => "Gravitic Shield",	
					11 => "Jump Engine",
					17 => "Structure",
					20 => "Primary",
			),
			3=> array(
					3 => "Thruster",
					4 => "Gravitic Shield",
					6 => "Light Particle Beam",
                        		8 => "Class-S Missile Rack",
					17 => "Structure",
					20 => "Primary",
			),
			4=> array(
					3 => "Thruster",
					4 => "Gravitic Shield",
					6 => "Light Particle Beam",
                        		8 => "Class-S Missile Rack",
					17 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
