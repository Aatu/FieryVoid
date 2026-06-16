<?php
class SshelathUsarra extends StarBaseSixSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 3500;
		$this->faction = "Escalation Wars Sshel'ath Alliance";
		$this->phpclass = "SshelathUsarra";
		$this->shipClass = "Usarra War Station";
		$this->fighters = array("normal"=>36); 
		$this->isd = 1969;
		$this->unofficial = true;
		$this->Enormous = true;

		$this->shipSizeClass = 3; 
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 20;
		$this->sideDefense = 20;

		$this->imagePath = "img/ships/EscalationWars/SshelathUsarra.png";
		$this->canvasSize = 280; //Enormous Starbase

		$this->locations = array(41, 42, 2, 32, 31, 1);
		$this->hitChart = array(			
			0=> array(
				11 => "Structure",
				13 => "EM Torpedo",
				16 => "Scanner",
				18 => "Reactor",
				20 => "TAG:C&C",
			),
		);

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(288); //pass magazine capacity - 20 rounds per launcher, plus reload rack 80
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 288); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-L

		$cnc = new CnC(5, 15, 0, 0);
		$cnc->startArc = 0;
		$cnc->endArc = 360;
        $this->addPrimarySystem($cnc);
		$cnc = new SecondaryCnC(5, 15, 0, 0);//all-around by default
        $this->addPrimarySystem($cnc);

		$this->addPrimarySystem(new Reactor(5, 18, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 16, 4, 7));
		$this->addPrimarySystem(new Scanner(5, 16, 4, 7));
        $this->addPrimarySystem(new EWEMTorpedo(5, 6, 5, 0, 360));
        $this->addPrimarySystem(new EWEMTorpedo(5, 6, 5, 0, 360));
        $this->addPrimarySystem(new EWEMTorpedo(5, 6, 5, 0, 360));
        $this->addPrimarySystem(new EWEMTorpedo(5, 6, 5, 0, 360));

		$this->addPrimarySystem(new Structure(5, 120));

		for ($i = 0; $i < sizeof($this->locations); $i++){

			$min = 0 + ($i*60);
			$max = 120 + ($i*60);

			$systems = array(
				new LaserCutter(4, 6, 4, $min, $max),
				new LaserCutter(4, 6, 4, $min, $max),
				new EWGatlingLaser(4, 7, 4, $min, $max),
				new EWGatlingLaser(4, 7, 4, $min, $max),
				new AmmoMissileRackSO(4, 0, 0, $min, $max, $ammoMagazine, true),
				new AmmoMissileRackSO(4, 0, 0, $min, $max, $ammoMagazine, true),
				new AmmoMissileRackSO(4, 0, 0, $min, $max, $ammoMagazine, true),
				new AmmoMissileRackSO(4, 0, 0, $min, $max, $ammoMagazine, true),
				new SubReactorUniversal(4, 20, 0, 0),
				new CargoBay(4, 20),
				new Hangar(4, 7, 6),
				new Structure(4, 96)
			);

			$loc = $this->locations[$i];

			$this->hitChart[$loc] = array(
				2 => "Class-SO Missile Rack",
				4 => "Laser Cutter",
				6 => "Gatling Laser",
				8 => "Cargo Bay",
				9 => "Sub Reactor",
				10 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			);

			foreach ($systems as $system){
				$this->addSystem($system, $loc);
			}
		}
    }
}
