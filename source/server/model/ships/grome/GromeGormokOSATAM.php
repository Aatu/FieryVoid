<?php
class GromeGormokOSATAM extends OSAT{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 150;
	$this->faction = "Grome Autocracy";
        $this->phpclass = "GromeGormokOSATAM";
        $this->imagePath = "img/ships/GromeGormok.png";
        $this->canvasSize = 60;
        $this->shipClass = 'Gormok Orbital Satellite';
		
	    $this->notes = 'Antiquated Sensors (cannot be boosted).';
	    $this->notes .= '<br>Targeting Array treated as a 1 point sensor.';
        
        $this->isd = 2240;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 11;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;

 	//ammo magazine itself (AND its missile options)
	$ammoMagazine = new AmmoMagazine(150); //pass magazine capacity 
	    $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
	    $ammoMagazine->addAmmoEntry(new AmmoHShellBasic(), 50); //add full load of basic shells  
	    $ammoMagazine->addAmmoEntry(new AmmoLShellBasic(), 100); //add full load of basic shells  
	    	    
		$this->enhancementOptionsEnabled[] = 'SHELL_HFLH';//add enhancement options for ammo - Heavy Flash Shell
	    $this->enhancementOptionsEnabled[] = 'SHELL_LFLH';//add enhancement options for ammo - Light Flash Shell
	    $this->enhancementOptionsEnabled[] = 'SHELL_HSCT';//add enhancement options for ammo - Heavy Scatter Shell	    
	    $this->enhancementOptionsEnabled[] = 'SHELL_LSCT';//add enhancement options for ammo - Light Scatter Shell
	    $this->enhancementOptionsEnabled[] = 'SHELL_HHVY';//add enhancement options for ammo - Heavy Heavy Shell	    
	    $this->enhancementOptionsEnabled[] = 'SHELL_LHVY';//add enhancement options for ammo - Light Heavy Shell		    	    
	    $this->enhancementOptionsEnabled[] = 'SHELL_HLR';//add enhancement options for ammo - Heavy Long Range Shell	    	
	    $this->enhancementOptionsEnabled[] = 'SHELL_HULR';//add enhancement options for ammo - Heavy Ultra Long Range Shell	

		
        $this->addPrimarySystem(new Reactor(3, 8, 0, 0));
        $this->addPrimarySystem(new AntiquatedScanner(3, 4, 2, 3)); 
		$targetingArray = new AntiquatedScanner(3, 6, 2, 1);
			$targetingArray->displayName = 'Targeting Array';
			$targetingArray->iconPath = "TargetingArray.png";
			$this->addPrimarySystem($targetingArray);
        $this->addPrimarySystem(new Thruster(4, 6, 0, 0, 2)); 
        $this->addPrimarySystem(new FlakCannon(2, 4, 2, 0, 360)); 
        $this->addPrimarySystem(new AmmoHeavyRailGun(3, 12, 9, 300, 60, $ammoMagazine));
        $this->addPrimarySystem(new AmmoLightRailGun(3, 6, 3, 180, 360, $ammoMagazine));
        $this->addPrimarySystem(new AmmoLightRailGun(3, 6, 3, 0, 180, $ammoMagazine));
		
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(3, 35));
        
		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					10 => "Thruster",
					11 => "Targeting Array",
					13 => "Heavy Railgun",
					15 => "Light Railgun",
					17 => "Scanner",
					19 => "Reactor",
					20 => "Flak Cannon",
			)
		);
    
    
        
    }
}
?>