<?php
class ChoukaPenanceBaseAM extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 550;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "Escalation Wars Chouka Theocracy";
		$this->phpclass = "ChoukaPenanceBaseAM";
		$this->shipClass = "Penance Military Base";
		$this->imagePath = "img/ships/EscalationWars/ChoukaTemple.png";
		$this->fighters = array("normal"=>12); 

		$this->shipSizeClass = 3; 
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 15;
		$this->sideDefense = 15;

		$this->canvasSize = 170; 
		$this->unofficial = true;

		$this->isd = 1966;

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(160); //pass magazine capacity - 20 rounds per launcher, plus reload rack 80
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 160); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-L

		$this->addPrimarySystem(new Reactor(4, 17, 0, 0));
		$this->addPrimarySystem(new CnC(4, 16, 0, 0)); 
		$this->addPrimarySystem(new Scanner(4, 12, 6, 6));
		$this->addPrimarySystem(new Hangar(4, 16));

		$this->addFrontSystem(new EWHeavyPointPlasmaGun(3, 7, 3, 270, 90));
        $this->addFrontSystem(new AmmoMissileRackS(3, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new AmmoMissileRackS(3, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new MediumPlasma(3, 5, 3, 270, 90));
		$this->addFrontSystem(new MediumPlasma(3, 5, 3, 270, 90));
		//$this->addFrontSystem(new CargoBay(3, 15));
		//$this->addFrontSystem(new CargoBay(3, 15));

			$cargoBay = new CargoBay(3, 15);
			$cargoBay->startArc = 270;
			$cargoBay->endArc = 90;
			$this->addFrontSystem($cargoBay);
			$this->addFrontSystem($cargoBay);
						
		$this->addAftSystem(new EWHeavyPointPlasmaGun(3, 7, 3, 90, 270));
        $this->addAftSystem(new AmmoMissileRackS(3, 0, 0, 90, 270, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addAftSystem(new AmmoMissileRackS(3, 0, 0, 90, 270, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addAftSystem(new MediumPlasma(3, 5, 3, 90, 270));
		$this->addAftSystem(new MediumPlasma(3, 5, 3, 90, 270));
		//$this->addAftSystem(new CargoBay(3, 15));
		//$this->addAftSystem(new CargoBay(3, 15));

			$cargoBay = new CargoBay(3, 15);
			$cargoBay->startArc = 90;
			$cargoBay->endArc = 270;
			$this->addAftSystem($cargoBay);
			$this->addAftSystem($cargoBay);
						
		$this->addLeftSystem(new EWHeavyPointPlasmaGun(3, 7, 3, 180, 360));
        $this->addLeftSystem(new AmmoMissileRackS(3, 0, 0, 180, 360, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addLeftSystem(new AmmoMissileRackS(3, 0, 0, 180, 360, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addLeftSystem(new MediumPlasma(3, 5, 3, 180, 360));
		$this->addLeftSystem(new MediumPlasma(3, 5, 3, 180, 360));
		//$this->addLeftSystem(new CargoBay(3, 15));
		//$this->addLeftSystem(new CargoBay(3, 15));

			$cargoBay = new CargoBay(3, 15);
			$cargoBay->startArc = 180;
			$cargoBay->endArc = 360;
			$this->addLeftSystem($cargoBay);
			$this->addLeftSystem($cargoBay);
						
		$this->addRightSystem(new EWHeavyPointPlasmaGun(3, 7, 3, 0, 180));
        $this->addRightSystem(new AmmoMissileRackS(3, 0, 0, 0, 180, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addRightSystem(new AmmoMissileRackS(3, 0, 0, 0, 180, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addRightSystem(new MediumPlasma(3, 5, 3, 0, 180));
		$this->addRightSystem(new MediumPlasma(3, 5, 3, 0, 180));
		//$this->addRightSystem(new CargoBay(3, 15));
		//$this->addRightSystem(new CargoBay(3, 15));

			$cargoBay = new CargoBay(3, 15);
			$cargoBay->startArc = 0;
			$cargoBay->endArc = 180;
			$this->addRightSystem($cargoBay);
			$this->addRightSystem($cargoBay);
						
		/*replaced by TAGed versions!
		$this->addFrontSystem(new Structure( 3, 50));
		$this->addAftSystem(new Structure( 3, 50));
		$this->addLeftSystem(new Structure( 3, 50));
		$this->addRightSystem(new Structure( 3, 50));
		$this->addPrimarySystem(new Structure( 4, 64));
		*/
		$this->addPrimarySystem(new Structure( 3, 64));//needs to be called first for some reason - static call apparently fails for the first time...
		$this->addFrontSystem(Structure::createAsOuter(3, 50, 270,90));
		$this->addAftSystem(Structure::createAsOuter(3, 50, 90, 270));
		$this->addLeftSystem(Structure::createAsOuter(3, 50, 180, 360));
		$this->addRightSystem(Structure::createAsOuter(3, 50, 0, 180));

		
		$this->hitChart = array(			
			0=> array(
				10 => "Structure",
				13 => "Scanner",
				16 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				3 => "TAG:Medium Plasma Cannon",
				5 => "TAG:Heavy Point Plasma Gun",
				9 => "TAG:Cargo Bay",
				11 => "TAG:Class-S Missile Rack",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				3 => "TAG:Medium Plasma Cannon",
				5 => "TAG:Heavy Point Plasma Gun",
				9 => "TAG:Cargo Bay",
				11 => "TAG:Class-S Missile Rack",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				3 => "TAG:Medium Plasma Cannon",
				5 => "TAG:Heavy Point Plasma Gun",
				9 => "TAG:Cargo Bay",
				11 => "TAG:Class-S Missile Rack",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				3 => "TAG:Medium Plasma Cannon",
				5 => "TAG:Heavy Point Plasma Gun",
				9 => "TAG:Cargo Bay",
				11 => "TAG:Class-S Missile Rack",
				18 => "Structure",
				20 => "Primary",
			),
		);



		
		}
    }
?>
