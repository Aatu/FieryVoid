<?php
class technicalTargetDrone extends BaseShip{
/* WARNING: prone to change!*/
    
	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);
		$this->pointCost = 10;
		$this->faction = "Custom Ships";
		$this->phpclass = "technicalTargetDrone";
//		$this->imagePath = "img/ships/BASurveyShip.png";
		$this->imagePath = "img/starwars/CloneWars/Venator.png";
		$this->shipClass = "Target Drone - DO NOT USE";
		$this->shipSizeClass = 3;
//		$this->canvasSize = 75; //img has 125px per side
		$this->forwardDefense = 20;
		$this->sideDefense = 20;
		$this->fighters = array("light"=>12);        
		$this->turncost = 0.5;
		$this->turndelaycost = 0.5;
		$this->accelcost = 2;
		$this->rollcost = 3;
		$this->pivotcost = 4;

//		$this->critRollMod += 1;
//		$this->enhancementOptionsDisabled[] = 'VULN_CRIT';
		
		$this->notes = "DO NOT USE, prone to change!";


	//ammo magazine itself (AND its missile options)
	$ammoMagazine = new AmmoMagazine(120); //pass magazine capacity 
	    $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
	    $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 120); //add full load of basic missiles
	    $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
	    $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-L
	    $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-L
	    $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-L
	    $this->enhancementOptionsEnabled[] = 'AMMO_P';//add enhancement options for other missiles - Class-P


		
//		$this->addPrimarySystem(new Particleimpeder(2, 0, 0, 180, 360));
//		$this->addPrimarySystem(new Particleimpeder(2, 0, 0, 0, 180));
		$reactor = new Reactor(6, 35, 0, 0);
			$reactor->markPowerFlux();
			$this->addPrimarySystem($reactor);
//		$this->addPrimarySystem(new Reactor(6, 35, 0, 0));
//		$cnc = new CnC(5, 20, 0, 0);
//			$cnc->markCommsFlux();
//			$this->addPrimarySystem($cnc);
//		$this->addPrimarySystem(new CnC(5, 20, 0, 0));
		$engine = new Engine(5, 20, 0, 20, 3);
			$engine->markEngineFlux();
			$this->addPrimarySystem($engine);
		$this->addPrimarySystem(new Engine(5, 50, 0, 20, 3));
		$this->addPrimarySystem(new Engine(5, 50, 0, 20, 3));
		$this->addPrimarySystem(new Engine(5, 50, 0, 20, 3));
		$this->addPrimarySystem(new Hangar(6, 100));
		$this->addPrimarySystem(new IonFieldGenerator(2, 0, 0, 0, 360));
		$this->addPrimarySystem(new IonFieldGenerator(2, 0, 0, 0, 360));
		$this->addPrimarySystem(new NexusAntifighterLauncher(2, 5, 0, 0, 360));
		
//        $this->addFrontSystem(new AntiquatedScanner(3, 20, 6, 6));
		$this->addFrontSystem(new CnC(6, 40, 0, 0));
		$this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
		$this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
//		$this->addFrontSystem(new BSGHybrid(0, 20, 0, 0));
		//$this->addFrontSystem(new Hangar(4, 6));
		
		//new weapon showcase
		$sensors = new Scanner(6, 103, 4, 10);
			$sensors->markHyach();
			$this->addFrontSystem($sensors); 
/*		$sensors = new ELINTScanner(6, 23, 4, 20);
			$sensors->markHyachELINT();
			$this->addFrontSystem($sensors); */
/*		$sensors = new Scanner(6, 23, 4, 20);
			$sensors->markSensorFlux();
			$this->addFrontSystem($sensors); */
        $this->addPrimarySystem(new SelfRepair(5, 6, 4)); //armor, structure, output


		$this->addPrimarySystem(new SpinalLaser(5, 12, 12, 330, 30));
//		$this->addPrimarySystem(new Stealth(1,1,0));
		
		

        $this->addFrontSystem(new Jammer(4, 180, 7));
		
        $this->addFrontSystem(new AmmoMissileRackR(3, 0, 0, 240, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new AmmoMissileRackR(3, 0, 0, 240, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new TestGun(1, 6, 3, 0, 360));
//		$this->addFrontSystem(new Enveloper(3, 8, 6, 300, 60));

		$this->addAftSystem(new Thruster(4, 10, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
		$this->addAftSystem(new Thruster(4, 10, 0, 3, 2));
		$this->addAftSystem(new JumpEngine(5, 20, 3, 20));
		$this->addLeftSystem(new Thruster(4, 14, 0, 5, 3));
		
		$this->addRightSystem(new Thruster(4, 14, 0, 5, 4));
		
		//0:primary, 1:front, 2:rear, 3:left, 4:right;
		$this->addFrontSystem(new Structure( 7, 100));
		$this->addAftSystem(new Structure( 5, 95));
		$this->addLeftSystem(new Structure( 4, 98));
		$this->addRightSystem(new Structure( 4, 98));
		$this->addPrimarySystem(new Structure( 6, 100));
		   
		//d20 hit chart
		$this->hitChart = array(
			
			0=> array(
				20 => "Hangar",
			/*
				10 => "Structure",
				13 => "Scanner",
				16 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
				*/
			),
			1=> array(
				20 => "Scanner",
				/*
				5 => "Plasma Wave",
				10 => "Stun Beam",
				15 => "Scattergun",
				20 => "3:Thruster", //front targets Port Thruster - but once destroyed, Front Structure shall be next
				*/
			),
			2=> array(
				20=>"Structure",
				/*
				5 => "Thruster",
				8 => "Jump Engine",
				10 => "Assault Laser",
				12 => "Twin Array",
				18 => "Structure",
				20 => "Primary",
				*/
			),
			3=> array( //Port
				20=>"Structure",
				/*
				10 => "2:Thruster", //Aft Twin Arrays
				20 => "0:Imperial Laser", //PRIMARY Imperial Lasers
				*/
			),
			4=> array( //Stbd
				20=>"Structure",
				/*
				10 => "2:Thruster", //Aft Twin Arrays
				20 => "0:Imperial Laser", //PRIMARY Imperial Lasers
				*/
			),
		);
    }
}
?>
