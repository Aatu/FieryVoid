<?php
class motenai1980AM extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 615;
		$this->faction = "Abbai Matriarchate (WotCR)";
        $this->phpclass = "motenai1980AM";
        $this->imagePath = "img/ships/AbbaiMotenai.png";
        $this->shipClass = "Motenai Heavy Mine Layer (1980)";
			$this->occurence = "common";
			$this->variantOf = 'Motenai Heavy Mine Layer';
        $this->shipSizeClass = 3;
		
        $this->minesweeperbonus = 3;    	

        $this->limited = 33;
        $this->isd = 1980;
        
        $this->forwardDefense = 18;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;

		//ammo magazine itself (AND its mine options)
		$ammoMagazine = new AmmoMagazine(30); //pass magazine capacity - 5 rounds per launcher, 30 mines.
	    $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
	    $ammoMagazine->addAmmoEntry(new AmmoBistifA(), 0); //add full load of basic missiles
	    $ammoMagazine->addAmmoEntry(new AmmoBistifB(), 0); //add full load of basic missiles 

		$this->enhancementOptionsEnabled[] = 'MINE_MLB';//add enhancement options for mines - Basic Mines
		$this->enhancementOptionsEnabled[] = 'MINE_MLW';//add enhancement options for mines - Wide-Range Mines
		$this->enhancementOptionsEnabled[] = 'IFF_SYS'; //Abilty to choose IFF enhancement.
		        
        $this->addPrimarySystem(new Reactor(4, 17, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 5, 6));  //+3 Minesweeper
        $this->addPrimarySystem(new Engine(4, 16, 0, 8, 4));
		$this->addPrimarySystem(new Hangar(4, 8));
        $this->addPrimarySystem(new ShieldGenerator(5, 16, 4, 4));
   
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 1, 300, 360));
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 1, 0, 60));
        $this->addFrontSystem(new LaserCutter(3, 6, 4, 300, 60));
        $this->addFrontSystem(new AbbaiMineLauncher(3, 6, 0, 300, 60, $ammoMagazine, false)); 
        $this->addFrontSystem(new AbbaiMineLauncher(3, 6, 0, 300, 60, $ammoMagazine, false)); 
        $this->addFrontSystem(new AbbaiMineLauncher(3, 6, 0, 300, 60, $ammoMagazine, false)); 
        $this->addFrontSystem(new AbbaiMineLauncher(3, 6, 0, 300, 60, $ammoMagazine, false)); 

        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 1, 180, 240));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 1, 120, 180));
        $this->addAftSystem(new JumpEngine(5, 12, 4, 32));

        $this->addLeftSystem(new AbbaiMineLauncher(3, 6, 0, 240, 360, $ammoMagazine, false)); 
        $this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        $this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        $this->addLeftSystem(new GraviticShield(0, 6, 0, 1, 240, 300));
        $this->addLeftSystem(new Thruster(3, 13, 0, 5, 3));

        $this->addRightSystem(new AbbaiMineLauncher(3, 6, 0, 0, 120, $ammoMagazine, false)); 
        $this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        $this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        $this->addRightSystem(new GraviticShield(0, 6, 0, 1, 60, 120));
        $this->addRightSystem(new Thruster(3, 13, 0, 5, 4));
        
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
					7 => "Laser Cutter",
               		10 => "Mine Launcher",
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
               		8 => "Mine Launcher",
					17 => "Structure",
					20 => "Primary",
			),
			4=> array(
					3 => "Thruster",
					4 => "Gravitic Shield",
					6 => "Light Particle Beam",
              		8 => "Mine Launcher",
					17 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>