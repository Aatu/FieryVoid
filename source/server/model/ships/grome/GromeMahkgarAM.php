<?php
class GromeMahkgarAM extends StarBaseSixSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 2500;
		$this->faction = "Grome Autocracy";
		$this->phpclass = "GromeMahkgarAM";
		$this->shipClass = "Mahkgar Starbase";
		$this->fighters = array("normal"=>36); 
        $this->isd = 2235;

	    $this->notes = 'Antiquated Sensors (cannot be boosted)';

		$this->shipSizeClass = 3;
        $this->Enormous = true;
		$this->iniativebonus = -200;
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 25;
		$this->sideDefense = 25;

		$this->imagePath = "img/ships/GromeMahkgar.png";
		$this->canvasSize = 260;

		$this->locations = array(41, 42, 2, 32, 31, 1);
		$this->hitChart = array(			
			0=> array(
				12 => "Structure",
				14 => "Targeting Array",
				16 => "Scanner",
				18 => "Reactor",
				20 => "TAG:C&C",
			),
		);

 	//ammo magazine itself (AND its missile options)
	$ammoMagazine = new AmmoMagazine(1500); //pass magazine capacity 
	    $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
	    $ammoMagazine->addAmmoEntry(new AmmoHShellBasic(), 300); //add full load of basic shells  
	    $ammoMagazine->addAmmoEntry(new AmmoMShellBasic(), 600); //add full load of basic shells  
	    $ammoMagazine->addAmmoEntry(new AmmoLShellBasic(), 600); //add full load of basic shells  
	    	    
		$this->enhancementOptionsEnabled[] = 'SHELL_HFLH';//add enhancement options for ammo - Heavy Flash Shell
	    $this->enhancementOptionsEnabled[] = 'SHELL_MFLH';//add enhancement options for ammo - Medium Flash Shell
	    $this->enhancementOptionsEnabled[] = 'SHELL_LFLH';//add enhancement options for ammo - Light Flash Shell
	    $this->enhancementOptionsEnabled[] = 'SHELL_HSCT';//add enhancement options for ammo - Heavy Scatter Shell	    
	    $this->enhancementOptionsEnabled[] = 'SHELL_MSCT';//add enhancement options for ammo - Medium Scatter Shell	
	    $this->enhancementOptionsEnabled[] = 'SHELL_LSCT';//add enhancement options for ammo - Light Scatter Shell
	    $this->enhancementOptionsEnabled[] = 'SHELL_HHVY';//add enhancement options for ammo - Heavy Heavy Shell	    
	    $this->enhancementOptionsEnabled[] = 'SHELL_MHVY';//add enhancement options for ammo - Medium Heavy Shell	
	    $this->enhancementOptionsEnabled[] = 'SHELL_LHVY';//add enhancement options for ammo - Light Heavy Shell		    	    
	    $this->enhancementOptionsEnabled[] = 'SHELL_HLR';//add enhancement options for ammo - Heavy Long Range Shell	    
	    $this->enhancementOptionsEnabled[] = 'SHELL_MLR';//add enhancement options for ammo - Medium Long Range Shell	
	    $this->enhancementOptionsEnabled[] = 'SHELL_HULR';//add enhancement options for ammo - Heavy Ultra Long Range Shell	

		$this->addPrimarySystem(new Reactor(4, 26, 0, 0));
		
		$cnc = new CnC(4, 25, 0, 0);
		$cnc->startArc = 0;
		$cnc->endArc = 360;
		$this->addPrimarySystem($cnc);
		$cnc = new SecondaryCnC(4, 25, 0, 0);//all-around by default
		$this->addPrimarySystem($cnc);
		
		$this->addPrimarySystem(new AntiquatedScanner(4, 24, 6, 6));
		$this->addPrimarySystem(new AntiquatedScanner(4, 24, 6, 6));
        $this->addPrimarySystem(new GromeTargetingArray(2, 0, 0, 0, 360, 3, false, true)); //Armor, health, power, startarc, endarc, output, escort, base	
        $this->addPrimarySystem(new GromeTargetingArray(2, 0, 0, 0, 360, 3, false, true)); //Armor, health, power, startarc, endarc, output, escort, base
        $this->addPrimarySystem(new GromeTargetingArray(2, 0, 0, 0, 360, 3, false, true)); //Armor, health, power, startarc, endarc, output, escort, base     	
        $this->addPrimarySystem(new GromeTargetingArray(2, 0, 0, 0, 360, 3, false, true)); //Armor, health, power, startarc, endarc, output, escort, base
        $this->addPrimarySystem(new GromeTargetingArray(2, 0, 0, 0, 360, 3, false, true)); //Armor, health, power, startarc, endarc, output, escort, base
        $this->addPrimarySystem(new GromeTargetingArray(2, 0, 0, 0, 360, 3, false, true)); //Armor, health, power, startarc, endarc, output, escort, base
		$this->addPrimarySystem(new Structure( 4, 240));

		for ($i = 0; $i < sizeof($this->locations); $i++){

			$min = 0 + ($i*60);
			$max = 120 + ($i*60);

			$struct = Structure::createAsOuter(4, 180,$min,$max);
			$hangar = new Hangar(4, 7, 6);
			$hangar->startArc = $min;
			$hangar->endArc = $max;
			$cargoBay = new CargoBay(4, 30);
			$cargoBay->startArc = $min;
			$cargoBay->endArc = $max;
			$subReactor = new SubReactorUniversal(4, 30, 0, 0);
			$subReactor->startArc = $min;
			$subReactor->endArc = $max;			
			/*$strut = new ConnectionStrut(4);
			$strut->startArc = $min;
			$strut->endArc = $max;			
			*/
			$systems = array(
				new AmmoHeavyRailGun(4, 12, 9, $min, $max, $ammoMagazine),
				new AmmoMediumRailGun(4, 9, 6, $min, $max, $ammoMagazine),
				new AmmoMediumRailGun(4, 9, 6, $min, $max, $ammoMagazine),
				new AmmoLightRailGun(4, 6, 3, $min, $max, $ammoMagazine),
				new AmmoLightRailGun(4, 6, 3, $min, $max, $ammoMagazine),
				new GromeFlakCannon(4, 4, 2, $min, $max),
				new GromeFlakCannon(4, 4, 2, $min, $max),
				$hangar,
				$cargoBay,
				$subReactor,
				new ConnectionStrut(4),
				$struct //new Structure(4, 180)
			);

			$loc = $this->locations[$i];

			$this->hitChart[$loc] = array(
				2 => "TAG:Flak Cannon",
				4 => "TAG:Light Railgun",
				6 => "TAG:Medium Railgun",
				7 => "TAG:Heavy Railgun",
				9 => "TAG:Cargo Bay",
				10 => "TAG:Hangar",
				11 => "TAG:Sub Reactor",
				13 => "Connection Strut",
				18 => "Structure",
				20 => "Primary",
			);

			foreach ($systems as $system){
				$this->addSystem($system, $loc);
			}
		}
    }
}