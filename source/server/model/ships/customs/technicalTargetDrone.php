<?php
class technicalTargetDrone extends BaseShip{
/* WARNING: prone to change!*/
    
	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);
		$this->pointCost = 10;
		$this->faction = "Custom Ships";
		$this->phpclass = "technicalTargetDrone";
//		$this->imagePath = "img/ships/BASurveyShip.png";
		$this->imagePath = "img/ships/Nexus/CraytanCorvette.png";
		$this->shipClass = "Target Drone - DO NOT USE";
//		$this->shipSizeClass = 3;
		$this->canvasSize = 75; //img has 125px per side
		$this->forwardDefense = 20;
		$this->sideDefense = 20;
		$this->fighters = array("light"=>12);        
		$this->turncost = 0.5;
		$this->turndelaycost = 0.5;
		$this->accelcost = 2;
		$this->rollcost = 3;
		$this->pivotcost = 4;

		
		$this->notes = "DO NOT USE, prone to change!";
		
		$this->addPrimarySystem(new Particleimpeder(2, 0, 0, 180, 360));
		$this->addPrimarySystem(new Particleimpeder(2, 0, 0, 0, 180));
		$this->addPrimarySystem(new Reactor(6, 35, 0, 0));
		$this->addPrimarySystem(new CnC(1, 1, 0, 0));
		$sensors = new Scanner(6, 23, 4, 20);
		$sensors->markAdvanced();
		$this->addFrontSystem($sensors);
		$this->addPrimarySystem(new Engine(5, 20, 0, 20, 3));
		$this->addPrimarySystem(new Hangar(6, 100));
		$this->addPrimarySystem(new IonFieldGenerator(2, 0, 0, 0, 360));
		$this->addPrimarySystem(new IonFieldGenerator(2, 0, 0, 0, 360));
		$this->addPrimarySystem(new IonFieldGenerator(2, 0, 0, 0, 360));
		
		$this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
		$this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
		//$this->addFrontSystem(new Hangar(4, 6));
		
		//new weapon showcase

//		$this->addFrontSystem(new PlasmaWeb(2, 4, 2, 0, 360));
//		$this->addFrontSystem(new PlasmaWeb(2, 4, 2, 0, 360));
//		$this->addFrontSystem(new PlasmaWeb(2, 4, 2, 0, 360));

//		$this->addFrontSystem(new PlasmaBlast(2, 4, 2, 0, 360));
//		$this->addFrontSystem(new Interdictor(2, 4, 1, 0, 360));
//		$this->addFrontSystem(new Interdictor(2, 4, 1, 0, 360));
		$this->addFrontSystem(new TrekPlasmaBurst(2, 4, 1, 0, 360));
		$this->addFrontSystem(new GraviticBolt(2, 5, 2, 0, 360));
		$this->addFrontSystem(new GraviticBolt(2, 5, 2, 0, 360));
		$this->addFrontSystem(new GraviticBolt(2, 5, 2, 0, 360));


		$this->addPrimarySystem(new SpinalLaser(5, 12, 12, 330, 30));
//		$this->addPrimarySystem(new Stealth(1,1,0));
		
/*		$this->addPrimarySystem(new AMissileRack(5, 6, 0, 0, 360));
		$this->addPrimarySystem(new BMissileRack(6, 9, 0, 0, 360));
        $this->addPrimarySystem(new MultiMissileLauncher(3, 'B', 0, 360));
		$this->addPrimarySystem(new LMissileRack(5, 6, 0, 0, 360));
        $this->addPrimarySystem(new MultiMissileLauncher(3, 'L', 0, 360));
		$this->addPrimarySystem(new LHMissileRack(5, 6, 0, 0, 360));
        $this->addPrimarySystem(new MultiMissileLauncher(3, 'LH', 0, 360));
        $this->addPrimarySystem(new SoMissileRack(3, 6, 0, 0, 360));
        $this->addPrimarySystem(new SMissileRack(3, 6, 0, 0, 360));
		$this->addPrimarySystem(new MultiMissileLauncher(3, 'S', 0, 360));
        $this->addPrimarySystem(new EWOMissileRack(3, 6, 0, 0, 360));
        $this->addPrimarySystem(new RMissileRack(3, 6, 0, 0, 360));
*/
		
		/*
		$this->addFrontSystem(new CustomERLightPBeam(3, 0, 0, 240, 120));
		$this->addFrontSystem(new CustomBPALight(3, 0, 0, 240, 120));
		$this->addFrontSystem(new CustomBPAMedium(3, 0, 0, 240, 120));
		$this->addFrontSystem(new CustomBPAHeavy(3, 0, 0, 240, 120));
		$this->addFrontSystem(new NexusKineticBoxLauncher(1, 0, 0, 270, 180));
		$this->addFrontSystem(new NexusChaffLauncher(1, 0, 0, 0, 360));
		*/
		/*
		$this->addAftSystem(new CommDisruptor(3, 0, 0, 0, 360));
		$this->addAftSystem(new CommJammer(3, 0, 0, 0, 360));		
		$this->addAftSystem(new ImpCommJammer(3, 0, 0, 0, 360));
		$this->addAftSystem(new SensorSpear(3, 0, 0, 0, 360));
		$this->addAftSystem(new SensorSpike(3, 0, 0, 0, 360));		
		$this->addAftSystem(new CombatLaser(3, 0, 0, 0, 360));	
		$this->addAftSystem(new LaserCutter(3, 0, 0, 0, 360));
		*/
		$this->addAftSystem(new Thruster(4, 10, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
		$this->addAftSystem(new Thruster(4, 10, 0, 3, 2));
		$this->addAftSystem(new JumpEngine(5, 20, 3, 20));
		/*
		$this->addAftSystem(new AssaultLaser(3, 6, 4, 180, 300));
		$this->addAftSystem(new AssaultLaser(3, 6, 4, 60, 180));
		$this->addAftSystem(new TwinArray(2, 16, 2, 120, 0));
		$this->addAftSystem(new TwinArray(2, 16, 2, 0, 240));
		*/
		$this->addLeftSystem(new Thruster(4, 14, 0, 5, 3));
		$this->addLeftSystem(new ImperialLaser(3, 8, 5, 300, 0));
		$this->addLeftSystem(new TwinArray(3, 6, 2, 180, 0));
		
		$this->addRightSystem(new Thruster(4, 14, 0, 5, 4));
		$this->addRightSystem(new ImperialLaser(3, 8, 5, 0, 60));
		$this->addRightSystem(new TwinArray(3, 6, 2, 0, 180));
		
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
				20 => "Structure",
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
