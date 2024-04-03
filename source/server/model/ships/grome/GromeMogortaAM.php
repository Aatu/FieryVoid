<?php
class GromeMogortaAM extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 650;
	$this->faction = "Grome Autocracy";
        $this->phpclass = "GromeMogortaAM";
        $this->imagePath = "img/ships/GromeMogorta.png";
        $this->shipClass = "Mogorta Warship";
        $this->shipSizeClass = 3;
		$this->canvasSize = 165; //img has 200px per side

	    $this->notes = 'Antiquated Sensors (cannot be boosted)';

        $this->fighters = array("normal"=>6);

		$this->isd = 2214;
        
        $this->forwardDefense = 16;
        $this->sideDefense = 17;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 4;
        $this->rollcost = 99; //cannot roll
        $this->pivotcost = 3;
        $this->iniativebonus = 0;

 	//ammo magazine itself (AND its missile options)
	$ammoMagazine = new AmmoMagazine(300); //pass magazine capacity 
	    $this->addPrimarySystem($ammoMagazine); //fit to ship immediately 
	    $ammoMagazine->addAmmoEntry(new AmmoMShellBasic(), 300); //add full load of basic shells   
	    	    
	    $this->enhancementOptionsEnabled[] = 'SHELL_MFLH';//add enhancement options for ammo - Medium Flash Shell	    
	    $this->enhancementOptionsEnabled[] = 'SHELL_MSCT';//add enhancement options for ammo - Medium Scatter Shell		    
	    $this->enhancementOptionsEnabled[] = 'SHELL_MHVY';//add enhancement options for ammo - Medium Heavy Shell		    
	    $this->enhancementOptionsEnabled[] = 'SHELL_MLR';//add enhancement options for ammo - Medium Long Range Shell	    
        
        $this->addPrimarySystem(new Reactor(4, 20, 0, 0));
        $this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new AntiquatedScanner(3, 16, 5, 6));
        $this->addPrimarySystem(new GromeTargetingArray(2, 0, 0, 0, 360, 2, false, false)); //Armor, health, power, startarc, endarc, output, escort, base	
        $this->addPrimarySystem(new GromeTargetingArray(2, 0, 0, 0, 360, 2, false, false)); //Armor, health, power, startarc, endarc, output, escort, base
        $this->addPrimarySystem(new Engine(4, 24, 0, 6, 4));
		$this->addPrimarySystem(new Hangar(2, 8));
		$this->addPrimarySystem(new JumpEngine(4, 20, 4, 36));
		
        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
		$this->addFrontSystem(new AmmoMediumRailGun(3, 9, 6, 300, 360, $ammoMagazine));
		$this->addFrontSystem(new AmmoMediumRailGun(3, 9, 6, 300, 360, $ammoMagazine));
		$this->addFrontSystem(new AmmoMediumRailGun(3, 9, 6, 0, 60, $ammoMagazine));
		$this->addFrontSystem(new AmmoMediumRailGun(3, 9, 6, 0, 60, $ammoMagazine));
        $this->addFrontSystem(new ConnectionStrut(4));

        $this->addAftSystem(new Thruster(3, 16, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 16, 0, 3, 2));
		$this->addAftSystem(new AmmoMediumRailGun(3, 9, 6, 180, 240, $ammoMagazine));
		$this->addAftSystem(new AmmoMediumRailGun(3, 9, 6, 120, 180, $ammoMagazine));
        $this->addAftSystem(new ConnectionStrut(3));

        $this->addLeftSystem(new FlakCannon(2, 4, 2, 180, 360));
        $this->addLeftSystem(new FlakCannon(2, 4, 2, 180, 360));
        $this->addLeftSystem(new FlakCannon(2, 4, 2, 180, 360));
        $this->addLeftSystem(new Thruster(3, 15, 0, 3, 3));
        $this->addLeftSystem(new ConnectionStrut(3));

        $this->addRightSystem(new FlakCannon(2, 4, 2, 0, 180));
        $this->addRightSystem(new FlakCannon(2, 4, 2, 0, 180));
        $this->addRightSystem(new FlakCannon(2, 4, 2, 0, 180));
        $this->addRightSystem(new Thruster(3, 15, 0, 3, 4));
        $this->addRightSystem(new ConnectionStrut(3));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 84));
        $this->addAftSystem(new Structure(3, 80));
        $this->addLeftSystem(new Structure(3, 100));
        $this->addRightSystem(new Structure(3, 100));
        $this->addPrimarySystem(new Structure(4, 75));
		
		$this->hitChart = array(
			0=> array(
					7 => "Structure",
					9 => "Targeting Array",
					11 => "Jump Engine",
					13 => "Engine",
					16 => "Scanner",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					8 => "Medium Railgun",
					15 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					10 => "Medium Railgun",
					15 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
			3=> array(
					6 => "Thruster",
					10 => "Flak Cannon",
					15 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
			4=> array(
					6 => "Thruster",
					10 => "Flak Cannon",
					15 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
		);
    }
}

?>
