<?php
class GromeTrokanMargusAM extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 1200; 
	$this->faction = "Grome Autocracy";
        $this->phpclass = "GromeTrokanMargusAM";
        $this->imagePath = "img/ships/GromeTrokan.png";
        $this->shipClass = "Trokan Margus Command Flagship";
			$this->variantOf = 'Trokan Flagship';
			$this->occurence = "unique";
		$this->limited = 10;
        $this->shipSizeClass = 3;
		$this->canvasSize = 180; //img has 200px per side

	    $this->notes = 'Antiquated Sensors (cannot be boosted).';
	    $this->notes .= '<br>Targeting Arrays treated as a 1 point sensors.';

        $this->fighters = array("normal"=>12);

		$this->isd = 2260;

		$this->enhancementOptionsDisabled[] = 'IMPR_REA';
		$this->enhancementOptionsDisabled[] = 'IMPR_ENG';
		$this->enhancementOptionsDisabled[] = 'IMPR_SENS';

 	//ammo magazine itself (AND its missile options)
	$ammoMagazine = new AmmoMagazine(450); //pass magazine capacity 
	    $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
	    $ammoMagazine->addAmmoEntry(new AmmoHShellBasic(), 250); //add full load of basic shells  
	    $ammoMagazine->addAmmoEntry(new AmmoMShellBasic(), 200); //add full load of basic shells  
	    	    
		$this->enhancementOptionsEnabled[] = 'SHELL_HFLH';//add enhancement options for ammo - Heavy Flash Shell
	    $this->enhancementOptionsEnabled[] = 'SHELL_MFLH';//add enhancement options for ammo - Medium Flash Shell
	    $this->enhancementOptionsEnabled[] = 'SHELL_HSCT';//add enhancement options for ammo - Heavy Scatter Shell	    
	    $this->enhancementOptionsEnabled[] = 'SHELL_MSCT';//add enhancement options for ammo - Medium Scatter Shell	
	    $this->enhancementOptionsEnabled[] = 'SHELL_HHVY';//add enhancement options for ammo - Heavy Heavy Shell	    
	    $this->enhancementOptionsEnabled[] = 'SHELL_MHVY';//add enhancement options for ammo - Medium Heavy Shell			    	    
	    $this->enhancementOptionsEnabled[] = 'SHELL_HLR';//add enhancement options for ammo - Heavy Long Range Shell	    
	    $this->enhancementOptionsEnabled[] = 'SHELL_MLR';//add enhancement options for ammo - Medium Long Range Shell	
	    $this->enhancementOptionsEnabled[] = 'SHELL_HULR';//add enhancement options for ammo - Heavy Ultra Long Range Shell	

        
        $this->forwardDefense = 18;
        $this->sideDefense = 19;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 4;
        $this->rollcost = 99; //cannot roll
        $this->pivotcost = 4;
        $this->iniativebonus = -15; 


		$reactor = new Reactor(5, 33, 0, 0);
			$reactor->markPowerFlux();
			$this->addPrimarySystem($reactor);
		$cnc = new CnC(4, 42, 0, 0);
			$cnc->markCommsFlux();
			$this->addPrimarySystem($cnc);
		$sensor = new AntiquatedScanner(4, 24, 6, 7);
			$sensor->markAntSensorFlux();
			$this->addPrimarySystem($sensor);
		$targetingArray = new AntiquatedScanner(2, 6, 2, 1);
			$targetingArray->displayName = 'Targeting Array';
			$targetingArray->iconPath = "TargetingArray.png";
			$this->addPrimarySystem($targetingArray);
		$targetingArray = new AntiquatedScanner(2, 6, 2, 1);
			$targetingArray->displayName = 'Targeting Array';
			$targetingArray->iconPath = "TargetingArray.png";
			$this->addPrimarySystem($targetingArray);
		$engine = new Engine(4, 36, 0, 12, 4);
			$engine->markEngineFlux();
			$this->addPrimarySystem($engine);
		$this->addPrimarySystem(new Hangar(3, 14));
		$this->addPrimarySystem(new JumpEngine(5, 20, 4, 36));
		
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
		$this->addFrontSystem(new AmmoHeavyRailGun(4, 12, 9, 330, 30, $ammoMagazine));
		$this->addFrontSystem(new FlakCannon(2, 4, 2, 240, 60));
		$this->addFrontSystem(new FlakCannon(2, 4, 2, 240, 60));
		$this->addFrontSystem(new FlakCannon(2, 4, 2, 300, 120));
		$this->addFrontSystem(new FlakCannon(2, 4, 2, 300, 120));
        $this->addFrontSystem(new ConnectionStrut(4));

        $this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 20, 0, 6, 2));
		$this->addAftSystem(new FlakCannon(2, 4, 2, 120, 300));
		$this->addAftSystem(new FlakCannon(2, 4, 2, 120, 300));
		$this->addAftSystem(new FlakCannon(2, 4, 2, 60, 240));
		$this->addAftSystem(new FlakCannon(2, 4, 2, 60, 240));
        $this->addAftSystem(new ConnectionStrut(4));

        $this->addLeftSystem(new FlakCannon(2, 4, 2, 180, 360));
        $this->addLeftSystem(new FlakCannon(2, 4, 2, 180, 360));
        $this->addLeftSystem(new FlakCannon(2, 4, 2, 180, 360));
        $this->addLeftSystem(new FlakCannon(2, 4, 2, 180, 360));
		$this->addLeftSystem(new AmmoHeavyRailGun(3, 12, 9, 300, 360, $ammoMagazine));
		$this->addLeftSystem(new AmmoMediumRailGun(3, 9, 6, 300, 360, $ammoMagazine));
		$this->addLeftSystem(new AmmoHeavyRailGun(3, 12, 9, 180, 240, $ammoMagazine));
		$this->addLeftSystem(new AmmoMediumRailGun(3, 9, 6, 180, 240, $ammoMagazine));
        $this->addLeftSystem(new Thruster(3, 10, 0, 3, 3));
        $this->addLeftSystem(new Thruster(3, 10, 0, 3, 3));
        $this->addLeftSystem(new ConnectionStrut(4));

        $this->addRightSystem(new FlakCannon(2, 4, 2, 0, 180));
        $this->addRightSystem(new FlakCannon(2, 4, 2, 0, 180));
        $this->addRightSystem(new FlakCannon(2, 4, 2, 0, 180));
        $this->addRightSystem(new FlakCannon(2, 4, 2, 0, 180));
		$this->addRightSystem(new AmmoHeavyRailGun(3, 12, 9, 0, 60, $ammoMagazine));
		$this->addRightSystem(new AmmoMediumRailGun(3, 0, 0, 0, 60, $ammoMagazine));
		$this->addRightSystem(new AmmoHeavyRailGun(3, 12, 9, 120, 180, $ammoMagazine));
		$this->addRightSystem(new AmmoMediumRailGun(3, 9, 6, 120, 180, $ammoMagazine));
        $this->addRightSystem(new Thruster(3, 10, 0, 3, 4));
        $this->addRightSystem(new Thruster(3, 10, 0, 3, 4));
        $this->addRightSystem(new ConnectionStrut(4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 108));
        $this->addAftSystem(new Structure(4, 90));
        $this->addLeftSystem(new Structure(4, 110));
        $this->addRightSystem(new Structure(4, 110));
        $this->addPrimarySystem(new Structure(5, 96));
		
		$this->hitChart = array(
			0=> array(
					6 => "Structure",
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
					12 => "Flak Cannon",
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
					7 => "Thruster",
					8 => "Medium Railgun",
					10 => "Heavy Railgun",
					12 => "Flak Cannon",
					15 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
			4=> array(
					7 => "Thruster",
					8 => "Medium Railgun",
					10 => "Heavy Railgun",
					12 => "Flak Cannon",
					15 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
		);
    }
}

?>
