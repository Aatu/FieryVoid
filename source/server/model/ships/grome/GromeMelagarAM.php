<?php
class GromeMelagarAM extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 500;
        $this->faction = "Grome Autocracy";
        $this->phpclass = "GromeMelagarAM";
        $this->imagePath = "img/ships/GromeMorgat.png";
        $this->shipClass = "Melagar Frigate Leader";
			$this->variantOf = "Morgat Heavy Frigate";
			$this->occurence = "uncommon";		
        $this->canvasSize = 75;
	    $this->isd = 2221;

	    $this->notes = 'Antiquated Sensors (cannot be boosted)';
        
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.50;
        $this->turndelaycost = 0.50;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        $this->iniativebonus = 65;
        
 	//ammo magazine itself (AND its missile options)
	$ammoMagazine = new AmmoMagazine(250); //pass magazine capacity 
	    $this->addPrimarySystem($ammoMagazine); //fit to ship immediately 
	    $ammoMagazine->addAmmoEntry(new AmmoMShellBasic(), 250); //add full load of basic shells   
	    	    
	    $this->enhancementOptionsEnabled[] = 'SHELL_MFLH';//add enhancement options for ammo - Medium Flash Shell    
	    $this->enhancementOptionsEnabled[] = 'SHELL_MSCT';//add enhancement options for ammo - Medium Scatter Shell		    
	    $this->enhancementOptionsEnabled[] = 'SHELL_MHVY';//add enhancement options for ammo - Medium Heavy Shell		    
	    $this->enhancementOptionsEnabled[] = 'SHELL_MLR';//add enhancement options for ammo - Medium Long Range Shell		

       
        $this->addPrimarySystem(new Reactor(3, 18, 0, 0));
        $this->addPrimarySystem(new CnC(3, 16, 0, 0));
        $this->addPrimarySystem(new AntiquatedScanner(3, 12, 4, 5));
        $this->addPrimarySystem(new GromeTargetingArray(2, 0, 0, 0, 360, 3, false, false)); //Armor, health, power, startarc, endarc, output, escort, base
        $this->addPrimarySystem(new Engine(3, 16, 0, 6, 3));
        $this->addPrimarySystem(new Hangar(2, 1));
        $this->addPrimarySystem(new Thruster(2, 13, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(2, 13, 0, 3, 4));     
        $this->addPrimarySystem(new ConnectionStrut(3));
        
		$this->addFrontSystem(new FlakCannon(2, 4, 2, 180, 60));
		$this->addFrontSystem(new AmmoMediumRailGun(2, 9, 6, 300, 60, $ammoMagazine));
		$this->addFrontSystem(new AmmoMediumRailGun(2, 9, 6, 300, 60, $ammoMagazine));
		$this->addFrontSystem(new AmmoMediumRailGun(2, 9, 6, 300, 60, $ammoMagazine));
		$this->addFrontSystem(new FlakCannon(2, 4, 2, 300, 180));
        $this->addFrontSystem(new Thruster(2, 10, 0, 3, 1));
	    
        $this->addAftSystem(new Thruster(2, 8, 0, 3, 2));    
        $this->addAftSystem(new Thruster(2, 8, 0, 3, 2));    
		$this->addAftSystem(new AmmoMediumRailGun(2, 9, 6, 120, 240, $ammoMagazine));
		$this->addAftSystem(new AmmoMediumRailGun(2, 9, 6, 120, 240, $ammoMagazine));
       
        $this->addPrimarySystem(new Structure(3, 85));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			4 => "Thruster",
			6 => "Connection Strut",
			8 => "Targeting Array",
			11 => "Engine",
			14 => "Scanner",
			16 => "Hangar",
			18 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			4 => "Thruster",
			7 => "Medium Railgun",
			9 => "Flak Cannon",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Thruster",
			9 => "Medium Railgun",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
