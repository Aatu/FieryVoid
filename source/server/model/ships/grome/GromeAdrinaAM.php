<?php
class GromeAdrinaAM extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 700;
	$this->faction = "Grome Autocracy";
        $this->phpclass = "GromeAdrinaAM";
        $this->imagePath = "img/ships/GromeMogorta.png";
        $this->shipClass = "Adrina War Escort";
			$this->variantOf = "Mogorta Warship";
			$this->occurence = "rare";          
        $this->shipSizeClass = 3;
		$this->canvasSize = 165; //img has 200px per side

	    $this->notes = 'Antiquated Sensors (cannot be boosted)';
	    $this->notes .= '<br>Haphazard Targeting Systems';	    

		$this->isd = 2253;
        
        $this->forwardDefense = 16;
        $this->sideDefense = 17;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 4;
        $this->rollcost = 99; //cannot roll
        $this->pivotcost = 3;
        $this->iniativebonus = 0;

 	//ammo magazine itself (AND its missile options)
	$ammoMagazine = new AmmoMagazine(100); //pass magazine capacity 
	    $this->addPrimarySystem($ammoMagazine); //fit to ship immediately 
	    $ammoMagazine->addAmmoEntry(new AmmoMShellBasic(), 100); //add full load of basic shells   
	    	    
	    $this->enhancementOptionsEnabled[] = 'SHELL_MFLH';//add enhancement options for ammo - Medium Flash Shell	    
	    $this->enhancementOptionsEnabled[] = 'SHELL_MSCT';//add enhancement options for ammo - Medium Scatter Shell		    
	    $this->enhancementOptionsEnabled[] = 'SHELL_MHVY';//add enhancement options for ammo - Medium Heavy Shell		    
	    $this->enhancementOptionsEnabled[] = 'SHELL_MLR';//add enhancement options for ammo - Medium Long Range Shell	    
        
        $this->addPrimarySystem(new Reactor(4, 20, 0, 0));
        $this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new AntiquatedScanner(3, 16, 5, 6));
        $targetingArray = new GromeTargetingArray(2, 0, 0, 0, 360, 3, true, false); //Armor, health, power, startarc, endarc, output, escort, base	
			$targetingArray->markHaphazard();
			$this->addPrimarySystem($targetingArray);
        $targetingArray = new GromeTargetingArray(2, 0, 0, 0, 360, 2, true, false); //Armor, health, power, startarc, endarc, output, escort, base	
			$targetingArray->markHaphazard();
			$this->addPrimarySystem($targetingArray);
        $targetingArray = new GromeTargetingArray(2, 0, 0, 0, 360, 2, true, false); //Armor, health, power, startarc, endarc, output, escort, base	
			$targetingArray->markHaphazard();
			$this->addPrimarySystem($targetingArray);							 
        $this->addPrimarySystem(new Engine(4, 24, 0, 6, 4));
		$this->addPrimarySystem(new Hangar(2, 2));
		$this->addPrimarySystem(new JumpEngine(4, 20, 4, 36));
		
        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
		$this->addFrontSystem(new AmmoMediumRailGun(3, 9, 6, 300, 360, $ammoMagazine));
		$this->addFrontSystem(new AmmoMediumRailGun(3, 9, 6, 0, 60, $ammoMagazine));
		$targetingArray = new GromeTargetingArray(2, 0, 0, 240, 60, 3, true, false); //Armor, health, power, startarc, endarc, output, escort, base	
			$targetingArray->markHaphazard();
			$this->addFrontSystem($targetingArray);
		$targetingArray = new GromeTargetingArray(2, 0, 0, 300, 120, 3, true, false); //Armor, health, power, startarc, endarc, output, escort, base	
			$targetingArray->markHaphazard();
			$this->addFrontSystem($targetingArray);					
        $this->addFrontSystem(new ConnectionStrut(4));

        $this->addAftSystem(new Thruster(3, 16, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 16, 0, 3, 2));
		$targetingArray = new GromeTargetingArray(2, 0, 0, 120, 300, 3, true, false); //Armor, health, power, startarc, endarc, output, escort, base	
			$targetingArray->markHaphazard();
			$this->addAftSystem($targetingArray);
		$targetingArray = new GromeTargetingArray(2, 0, 0, 60, 240, 3, true, false); //Armor, health, power, startarc, endarc, output, escort, base	
			$targetingArray->markHaphazard();
			$this->addAftSystem($targetingArray);  			        		
        $this->addAftSystem(new ConnectionStrut(3));

        $this->addLeftSystem(new GromeFlakCannon(2, 4, 2, 180, 360));
        $this->addLeftSystem(new GromeFlakCannon(2, 4, 2, 180, 360));
        $this->addLeftSystem(new GromeFlakCannon(2, 4, 2, 180, 360));
        $this->addLeftSystem(new Thruster(3, 15, 0, 3, 3));
        $this->addLeftSystem(new ConnectionStrut(3));

        $this->addRightSystem(new GromeFlakCannon(2, 4, 2, 0, 180));
        $this->addRightSystem(new GromeFlakCannon(2, 4, 2, 0, 180));
        $this->addRightSystem(new GromeFlakCannon(2, 4, 2, 0, 180));
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
					6 => "Targeting Array",					
					8 => "Medium Railgun",
					15 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					10 => "Targeting Array",	
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
