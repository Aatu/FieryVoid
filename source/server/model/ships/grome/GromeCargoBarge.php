<?php
class GromeCargoBarge extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 250;
		$this->faction = "Civilians";
        $this->phpclass = "GromeCargoBarge";
        $this->imagePath = "img/ships/GromeCargoBarge.png";
        $this->shipClass = "Grome Cargo Carge";
        $this->shipSizeClass = 3;
		$this->canvasSize = 200; 
		
	    $this->isCombatUnit = false; //not a combat unit, it will never be present in a regular battlegroup

	    $this->notes = 'Antiquated Sensors (cannot be boosted)';
		
	    $this->notes = '<br>Unreliable Ship:';
   	    $this->notes .= '<br> - Vulnerable to Criticals';
 	    $this->notes .= '<br> - Sluggish';
		
		$this->fighters = array("cargo shuttles"=>6);     

		$this->isd = 2218;
        
		
		$this->critRollMod += 1;
		$this->iniativebonus = -7;
		$this->enhancementOptionsDisabled[] = 'VULN_CRIT';
		$this->enhancementOptionsDisabled[] = 'SLUGGISH';

        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 4;
        $this->rollcost = 99; //cannot roll
        $this->pivotcost = 99;
        $this->iniativebonus = -11*5;

 	//ammo magazine itself (AND its missile options)
	$ammoMagazine = new AmmoMagazine(100); //pass magazine capacity 
	    $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
	    $ammoMagazine->addAmmoEntry(new AmmoLShellBasic(), 100); //add full load of basic shells  
 
	    	    
		$this->enhancementOptionsEnabled[] = 'SHELL_HFLH';//add enhancement options for ammo - Heavy Flash Shell
	    $this->enhancementOptionsEnabled[] = 'SHELL_MFLH';//add enhancement options for ammo - Medium Flash Shell
	    $this->enhancementOptionsEnabled[] = 'SHELL_HSCT';//add enhancement options for ammo - Heavy Scatter Shell	    
	    $this->enhancementOptionsEnabled[] = 'SHELL_MSCT';//add enhancement options for ammo - Medium Scatter Shell	
	    $this->enhancementOptionsEnabled[] = 'SHELL_HHVY';//add enhancement options for ammo - Heavy Heavy Shell	    
	    $this->enhancementOptionsEnabled[] = 'SHELL_MHVY';//add enhancement options for ammo - Medium Heavy Shell 
	    $this->enhancementOptionsEnabled[] = 'SHELL_HLR';//add enhancement options for ammo - Heavy Long Range Shell	    
	    $this->enhancementOptionsEnabled[] = 'SHELL_MLR';//add enhancement options for ammo - Medium Long Range Shell	
	    $this->enhancementOptionsEnabled[] = 'SHELL_HULR';//add enhancement options for ammo - Heavy Ultra Long Range Shell	
        
        $this->addPrimarySystem(new Reactor(3, 11, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new AntiquatedScanner(3, 10, 4, 2));
        $this->addPrimarySystem(new GromeTargetingArray(2, 0, 0, 0, 360, 2, false, false)); //Armor, health, power, startarc, endarc, output, escort, base	
        $this->addPrimarySystem(new Engine(4, 28, 0, 8, 4));
		$this->addPrimarySystem(new Hangar(3, 6));
		$this->addPrimarySystem(new GromeFlakCannon(2, 4, 2, 0, 360));


        $this->addFrontSystem(new Thruster(3, 14, 0, 4, 1));
        $this->addFrontSystem(new ConnectionStrut(4));

        $this->addAftSystem(new Thruster(3, 15, 0, 4, 2));
        $this->addAftSystem(new ConnectionStrut(4));


        $this->addLeftSystem(new Thruster(3, 13, 0, 4, 3));
        $this->addLeftSystem(new ConnectionStrut(4));
        $this->addLeftSystem(new CargoBay(2,147));
		$this->addLeftSystem(new AmmoLightRailGun(2, 6, 3, 240, 0, $ammoMagazine));

        $this->addRightSystem(new Thruster(3, 13, 0, 4, 4));
        $this->addRightSystem(new ConnectionStrut(4));
        $this->addRightSystem(new CargoBay(2,147));
		$this->addRightSystem(new AmmoLightRailGun(2, 6, 3, 0, 120, $ammoMagazine));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 36));
        $this->addAftSystem(new Structure(4, 36));
        $this->addLeftSystem(new Structure(4, 56));
        $this->addRightSystem(new Structure(4, 56));
        $this->addPrimarySystem(new Structure(4, 40));
		
		$this->hitChart = array(
			0=> array(
					9 => "Structure",
					11 => "Flak Cannon",
					12 => "Targeting Array",
					13 => "Engine",
					16 => "Scanner",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					5 => "Thruster",
					15 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					15 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					7 => "Cargo Bay",
					9 => "Light Railgun",
					15 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					7 => "Cargo Bay",
					9 => "Light Railgun",
					15 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
		);
    }
}

?>
