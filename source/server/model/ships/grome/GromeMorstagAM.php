<?php
class GromeMorstagAM extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 400;
        $this->faction = "Grome Autocracy";
        $this->phpclass = "GromeMorstagAM";
        $this->imagePath = "img/ships/GromeMorgat.png";
        $this->shipClass = "Morstag Escort Frigate";
			$this->variantOf = "Morgat Heavy Frigate";
			$this->occurence = "uncommon";        
        $this->canvasSize = 75;
	    $this->isd = 2237;

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
	$ammoMagazine = new AmmoMagazine(200); //pass magazine capacity 
	    $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
	    $ammoMagazine->addAmmoEntry(new AmmoLShellBasic(), 200); //add full load of basic shells  
	    	    
	    $this->enhancementOptionsEnabled[] = 'SHELL_LFLH';//add enhancement options for ammo - Light Flash Shell  
	    $this->enhancementOptionsEnabled[] = 'SHELL_LSCT';//add enhancement options for ammo - Light Scatter Shell   	
	    $this->enhancementOptionsEnabled[] = 'SHELL_LHVY';//add enhancement options for ammo - Light Heavy Shell	    
         
        $this->addPrimarySystem(new Reactor(3, 18, 0, 0));
        $this->addPrimarySystem(new CnC(3, 12, 0, 0));
        $this->addPrimarySystem(new AntiquatedScanner(3, 12, 4, 5));
        $this->addPrimarySystem(new GromeTargetingArray(2, 0, 0, 0, 360, 2, true, false)); //Armor, health, power, startarc, endarc, output, escort, base	
        $this->addPrimarySystem(new GromeTargetingArray(2, 0, 0, 0, 360, 2, true, false)); //Armor, health, power, startarc, endarc, output, escort, base	        
        $this->addPrimarySystem(new Engine(3, 16, 0, 6, 3));
        $this->addPrimarySystem(new Hangar(2, 1));
        $this->addPrimarySystem(new Thruster(2, 13, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(2, 13, 0, 3, 4));     
        $this->addPrimarySystem(new ConnectionStrut(3));
        
		$this->addFrontSystem(new AmmoLightRailGun(2, 6, 3, 300, 60, $ammoMagazine));
		$this->addFrontSystem(new AmmoLightRailGun(2, 6, 3, 300, 60, $ammoMagazine));
		$this->addFrontSystem(new GromeFlakCannon(2, 4, 2, 180, 60));
		$this->addFrontSystem(new GromeFlakCannon(2, 4, 2, 300, 180));
        $this->addFrontSystem(new GromeTargetingArray(2, 0, 0, 240, 120, 3, true, false)); //Armor, health, power, startarc, endarc, output, escort, base	  		
        $this->addFrontSystem(new Thruster(2, 10, 0, 3, 1));
	    
        $this->addAftSystem(new Thruster(2, 8, 0, 3, 2));    
        $this->addAftSystem(new Thruster(2, 8, 0, 3, 2));    
		$this->addAftSystem(new AmmoLightRailGun(2, 6, 3, 120, 240, $ammoMagazine));
		$this->addAftSystem(new AmmoLightRailGun(2, 6, 3, 120, 240, $ammoMagazine));
       
        $this->addPrimarySystem(new Structure(3, 85));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			4 => "Thruster",
			6 => "Connection Strut",
			9 => "Targeting Array",
			12 => "Engine",
			15 => "Scanner",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			4 => "Thruster",
			5 => "Targeting Array",
			7 => "Light Railgun",
			9 => "Flak Cannon",			
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
