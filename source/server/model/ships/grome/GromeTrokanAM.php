<?php
class GromeTrokanAM extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 900;
	$this->faction = "Grome Autocracy";
        $this->phpclass = "GromeTrokanAM";
        $this->imagePath = "img/ships/GromeTrokan.png";
        $this->shipClass = "Trokan Flagship";
			$this->limited = 10;
        $this->shipSizeClass = 3;
		$this->canvasSize = 180; //img has 200px per side

	    $this->notes = 'Antiquated Sensors (cannot be boosted)';

        $this->fighters = array("normal"=>12);

		$this->isd = 2238;
        
        $this->forwardDefense = 18;
        $this->sideDefense = 19;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 4;
        $this->rollcost = 99; //cannot roll
        $this->pivotcost = 4;
        $this->iniativebonus = 0;
 
 	//ammo magazine itself (AND its missile options)
	$ammoMagazine = new AmmoMagazine(450); //pass magazine capacity 
	    $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
	    $ammoMagazine->addAmmoEntry(new AmmoHShellBasic(), 50); //add full load of basic shells  
	    $ammoMagazine->addAmmoEntry(new AmmoMShellBasic(), 400); //add full load of basic shells   
	    	    
		$this->enhancementOptionsEnabled[] = 'SHELL_HFLH';//add enhancement options for ammo - Heavy Flash Shell
	    $this->enhancementOptionsEnabled[] = 'SHELL_MFLH';//add enhancement options for ammo - Medium Flash Shell
	    $this->enhancementOptionsEnabled[] = 'SHELL_HSCT';//add enhancement options for ammo - Heavy Scatter Shell	    
	    $this->enhancementOptionsEnabled[] = 'SHELL_MSCT';//add enhancement options for ammo - Medium Scatter Shell	
	    $this->enhancementOptionsEnabled[] = 'SHELL_HHVY';//add enhancement options for ammo - Heavy Heavy Shell	    
	    $this->enhancementOptionsEnabled[] = 'SHELL_MHVY';//add enhancement options for ammo - Medium Heavy Shell	
	    $this->enhancementOptionsEnabled[] = 'SHELL_HLR';//add enhancement options for ammo - Heavy Long Range Shell	    
	    $this->enhancementOptionsEnabled[] = 'SHELL_MLR';//add enhancement options for ammo - Medium Long Range Shell	
	    $this->enhancementOptionsEnabled[] = 'SHELL_HULR';//add enhancement options for ammo - Heavy Ultra Long Range Shell		    

	            
        $this->addPrimarySystem(new Reactor(4, 25, 0, 0));
        $this->addPrimarySystem(new CnC(4, 30, 0, 0));
        $this->addPrimarySystem(new AntiquatedScanner(4, 20, 6, 6));
        $this->addPrimarySystem(new GromeTargetingArray(2, 0, 0, 0, 360, 2, false, false)); //Armor, health, power, startarc, endarc, output, escort, base	
        $this->addPrimarySystem(new GromeTargetingArray(2, 0, 0, 0, 360, 2, false, false)); //Armor, health, power, startarc, endarc, output, escort, base
        $this->addPrimarySystem(new Engine(4, 32, 0, 10, 4));
		$this->addPrimarySystem(new Hangar(3, 14));
		$this->addPrimarySystem(new JumpEngine(4, 20, 4, 36));
		
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
		$this->addFrontSystem(new AmmoHeavyRailGun(4, 0, 0, 330, 30, $ammoMagazine));	
		$this->addFrontSystem(new GromeFlakCannon(2, 4, 2, 240, 60));
		$this->addFrontSystem(new GromeFlakCannon(2, 4, 2, 300, 120));
        $this->addFrontSystem(new ConnectionStrut(4));

        $this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 16, 0, 4, 2));
		$this->addAftSystem(new GromeFlakCannon(2, 4, 2, 120, 300));
		$this->addAftSystem(new GromeFlakCannon(2, 4, 2, 60, 240));
        $this->addAftSystem(new ConnectionStrut(4));

        $this->addLeftSystem(new GromeFlakCannon(2, 4, 2, 180, 360));
        $this->addLeftSystem(new GromeFlakCannon(2, 4, 2, 180, 360));
        $this->addLeftSystem(new GromeFlakCannon(2, 4, 2, 180, 360));
		$this->addLeftSystem(new AmmoMediumRailGun(3, 0, 0, 300, 360, $ammoMagazine));
		$this->addLeftSystem(new AmmoMediumRailGun(3, 0, 0, 300, 360, $ammoMagazine));
		$this->addLeftSystem(new AmmoMediumRailGun(3, 0, 0, 180, 240, $ammoMagazine));
		$this->addLeftSystem(new AmmoMediumRailGun(3, 0, 0, 180, 240, $ammoMagazine));
        $this->addLeftSystem(new Thruster(3, 10, 0, 3, 3));
        $this->addLeftSystem(new Thruster(3, 10, 0, 3, 3));
        $this->addLeftSystem(new ConnectionStrut(4));

        $this->addRightSystem(new GromeFlakCannon(2, 4, 2, 0, 180));
        $this->addRightSystem(new GromeFlakCannon(2, 4, 2, 0, 180));
        $this->addRightSystem(new GromeFlakCannon(2, 4, 2, 0, 180));
		$this->addRightSystem(new AmmoMediumRailGun(3, 0, 0, 0, 60, $ammoMagazine));
		$this->addRightSystem(new AmmoMediumRailGun(3, 0, 0, 0, 60, $ammoMagazine));
		$this->addRightSystem(new AmmoMediumRailGun(3, 0, 0, 120, 180, $ammoMagazine));
		$this->addRightSystem(new AmmoMediumRailGun(3, 0, 0, 120, 180, $ammoMagazine));
        $this->addRightSystem(new Thruster(3, 10, 0, 3, 4));
        $this->addRightSystem(new Thruster(3, 10, 0, 3, 4));
        $this->addRightSystem(new ConnectionStrut(4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 108));
        $this->addAftSystem(new Structure(4, 90));
        $this->addLeftSystem(new Structure(4, 110));
        $this->addRightSystem(new Structure(4, 110));
        $this->addPrimarySystem(new Structure(4, 88));
		
		$this->hitChart = array(
			0=> array(
					7 => "Structure",
					9 => "Targeting Array",
					11 => "Jump Engine",
					13 => "Engine",
					15 => "Scanner",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					5 => "Thruster",
					7 => "Heavy Railgun",
					11 => "Flak Cannon",
					15 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					10 => "Flak Cannon",
					15 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
			3=> array(
					6 => "Thruster",
					9 => "Medium Railgun",
					12 => "Flak Cannon",
					15 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
			4=> array(
					6 => "Thruster",
					9 => "Medium Railgun",
					12 => "Flak Cannon",
					15 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
		);
    }
}

?>
