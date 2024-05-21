<?php
class GromeMorgatAM extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 450;
        $this->faction = "Grome Autocracy";
        $this->phpclass = "GromeMorgatAM";
        $this->imagePath = "img/ships/GromeMorgat.png";
        $this->shipClass = "Morgat Heavy Frigate";
        $this->canvasSize = 75;
	    $this->isd = 2218;

	    $this->notes = 'Antiquated Sensors (cannot be boosted)';
        
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.50;
        $this->turndelaycost = 0.50;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        $this->iniativebonus = 60;
        
 	//ammo magazine itself (AND its missile options)
	$ammoMagazine = new AmmoMagazine(350); //pass magazine capacity 
	    $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
	    $ammoMagazine->addAmmoEntry(new AmmoMShellBasic(), 50); //add full load of basic shells  
	    $ammoMagazine->addAmmoEntry(new AmmoLShellBasic(), 300); //add full load of basic shells  
	    	    
	    $this->enhancementOptionsEnabled[] = 'SHELL_MFLH';//add enhancement options for ammo - Medium Flash Shell
	    $this->enhancementOptionsEnabled[] = 'SHELL_LFLH';//add enhancement options for ammo - Light Flash Shell  
	    $this->enhancementOptionsEnabled[] = 'SHELL_MSCT';//add enhancement options for ammo - Medium Scatter Shell	
	    $this->enhancementOptionsEnabled[] = 'SHELL_LSCT';//add enhancement options for ammo - Light Scatter Shell   
	    $this->enhancementOptionsEnabled[] = 'SHELL_MHVY';//add enhancement options for ammo - Medium Heavy Shell	
	    $this->enhancementOptionsEnabled[] = 'SHELL_LHVY';//add enhancement options for ammo - Light Heavy Shell	    
	    $this->enhancementOptionsEnabled[] = 'SHELL_MLR';//add enhancement options for ammo - Medium Long Range Shell	
         
        $this->addPrimarySystem(new Reactor(3, 18, 0, 0));
        $this->addPrimarySystem(new CnC(3, 12, 0, 0));
        $this->addPrimarySystem(new AntiquatedScanner(3, 12, 4, 5));
        $this->addPrimarySystem(new GromeTargetingArray(2, 0, 0, 0, 360, 2, false, false)); //Armor, health, power, startarc, endarc, output, escort, base
        $this->addPrimarySystem(new Engine(3, 16, 0, 6, 3));
        $this->addPrimarySystem(new Hangar(2, 1));
		$this->addPrimarySystem(new GromeFlakCannon(2, 4, 2, 0, 360));
        $this->addPrimarySystem(new Thruster(2, 13, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(2, 13, 0, 3, 4));     
        $this->addPrimarySystem(new ConnectionStrut(3));
        
		$this->addFrontSystem(new AmmoLightRailGun(2, 6, 3, 300, 60, $ammoMagazine));
		$this->addFrontSystem(new AmmoLightRailGun(2, 6, 3, 300, 60, $ammoMagazine));
		$this->addFrontSystem(new AmmoLightRailGun(2, 6, 3, 300, 60, $ammoMagazine));
		$this->addFrontSystem(new AmmoLightRailGun(2, 6, 3, 300, 60, $ammoMagazine));
		$this->addFrontSystem(new AmmoMediumRailGun(2, 9, 6, 300, 60, $ammoMagazine));
        $this->addFrontSystem(new Thruster(2, 10, 0, 3, 1));
	    
        $this->addAftSystem(new Thruster(2, 8, 0, 3, 2));    
        $this->addAftSystem(new Thruster(2, 8, 0, 3, 2));    
		$this->addAftSystem(new LightRailGun(2, 6, 3, 120, 240));
		$this->addAftSystem(new LightRailGun(2, 6, 3, 120, 240));
       
        $this->addPrimarySystem(new Structure(3, 85));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			4 => "Thruster",
			6 => "Connection Strut",
			7 => "Targeting Array",
			9 => "Flak Cannon",
			12 => "Engine",
			15 => "Scanner",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			4 => "Thruster",
			5 => "Medium Railgun",
			9 => "Light Railgun",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Thruster",
			9 => "Light Railgun",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
