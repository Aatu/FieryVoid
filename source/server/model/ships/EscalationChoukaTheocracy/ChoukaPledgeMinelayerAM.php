<?php
class ChoukaPledgeMinelayerAM extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 175;
        $this->faction = "Escalation Wars Chouka Theocracy";
        $this->phpclass = "ChoukaPledgeMinelayerAM";
        $this->imagePath = "img/ships/EscalationWars/ChoukaRevelation.png";
        $this->shipClass = "Pledge Minelayer";
			$this->variantOf = "Revelation War Barge";
			$this->occurence = "uncommon";		
		$this->unofficial = true;
        $this->canvasSize = 75;
	    $this->isd = 1952;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = -20;

		$this->IFFSystem = false;        

	//ammo magazine itself (AND its missile options)
	$ammoMagazine = new AmmoMagazine(32); //80+20 Basic, 80 Intercept and up to 40 Mines
	    $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
	    $ammoMagazine->addAmmoEntry(new AmmoVedasA(), 0); //add full load of basic missiles
//	    $ammoMagazine->addAmmoEntry(new AmmoVedasB(), 0); //add full load of basic missiles
//	    $ammoMagazine->addAmmoEntry(new AmmoVedasC(), 0); //add full load of basic missiles

		$this->enhancementOptionsEnabled[] = 'MINE_AML';//add enhancement options for mines - Vedas-A Mines
		$this->enhancementOptionsEnabled[] = 'MINE_BML';//add enhancement options for mines - Vedas-B Mines
		$this->enhancementOptionsEnabled[] = 'MINE_CML';//add enhancement options for mines - Vedas-C Mines
		$this->enhancementOptionsEnabled[] = 'IFF_SYS'; //Abilty to choose IFF enhancement.		 		 		  	    	    	    
         
		$this->addPrimarySystem(new Reactor(3, 9, 0, 0));
        $this->addPrimarySystem(new CnC(3, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 4, 4));
        $this->addPrimarySystem(new Engine(3, 10, 0, 6, 2));
        $this->addPrimarySystem(new Hangar(3, 4, 1));
		$this->addPrimarySystem(new CargoBay(3, 16));
        $this->addPrimarySystem(new Thruster(2, 11, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(2, 11, 0, 3, 4));        
        
		$this->addFrontSystem(new ChoukaMineLauncher(2, 0, 0, 300, 60, $ammoMagazine, false));
		$this->addFrontSystem(new ChoukaMineLauncher(2, 0, 0, 300, 60, $ammoMagazine, false));
        $this->addFrontSystem(new LightPlasma(2, 4, 2, 300, 60));	
		$this->addFrontSystem(new EWPointPlasmaGun(1, 3, 2, 180, 360));
		$this->addFrontSystem(new EWPointPlasmaGun(1, 3, 2, 0, 180));
        $this->addFrontSystem(new Thruster(2, 13, 0, 3, 1));

		$this->addAftSystem(new ChoukaMineLauncher(2, 0, 0, 120, 240, $ammoMagazine, false));
		$this->addAftSystem(new ChoukaMineLauncher(2, 0, 0, 120, 240, $ammoMagazine, false));
		$this->addAftSystem(new EWPointPlasmaGun(1, 3, 2, 180, 360));
		$this->addAftSystem(new EWPointPlasmaGun(1, 3, 2, 0, 180));
        $this->addAftSystem(new Thruster(2, 18, 0, 6, 2));    
       
        $this->addPrimarySystem(new Structure(3, 56));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			8 => "Thruster",
			11 => "Cargo Bay",
			13 => "Scanner",
			15 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			4 => "Thruster",
			5 => "Light Plasma Cannon",
			7 => "Point Plasma Gun",
			11 => "Mine Launcher",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			5 => "Thruster",
			7 => "Point Plasma Gun",
			11 => "Mine Launcher",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
