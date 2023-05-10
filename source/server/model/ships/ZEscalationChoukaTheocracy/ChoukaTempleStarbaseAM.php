<?php
class ChoukaTempleStarbaseAM extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 475;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "ZEscalation Chouka Theocracy";
		$this->phpclass = "ChoukaTempleStarbaseAM";
		$this->shipClass = "Temple Starbase";
		$this->imagePath = "img/ships/EscalationWars/ChoukaTemple.png";
		$this->fighters = array("normal"=>12); 

//		$this->shipSizeClass = 3; 
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 18;
		$this->sideDefense = 18;

		$this->canvasSize = 150; 
		$this->unofficial = true;

		$this->isd = 1932;

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(48); //pass magazine capacity - 20 rounds per launcher, plus reload rack 80
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 48); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-L

		$this->addPrimarySystem(new Reactor(4, 20, 0, 0));
		$this->addPrimarySystem(new CnC(4, 24, 0, 0)); 
		$this->addPrimarySystem(new Scanner(4, 16, 7, 7));
		$this->addPrimarySystem(new Hangar(4, 16));
		$this->addPrimarySystem(new CargoBay(4, 30));
		$this->addPrimarySystem(new Quarters(4, 8));
		$this->addPrimarySystem(new Quarters(4, 8));

		$this->addFrontSystem(new HeavyPlasma(3, 8, 5, 270, 90));
		$this->addFrontSystem(new AmmoMissileRackSO(3, 0, 0, 270, 90, $ammoMagazine, true));
		$this->addFrontSystem(new MediumPlasma(3, 5, 3, 270, 90));
		$this->addFrontSystem(new MediumPlasma(3, 5, 3, 270, 90));

		$this->addAftSystem(new HeavyPlasma(3, 8, 5, 90, 270));
		$this->addAftSystem(new AmmoMissileRackSO(3, 0, 0, 90, 270, $ammoMagazine, true));
		$this->addAftSystem(new MediumPlasma(3, 5, 3, 90, 270));
		$this->addAftSystem(new MediumPlasma(3, 5, 3, 90, 270));

		$this->addLeftSystem(new HeavyPlasma(3, 8, 5, 180, 360));
		$this->addLeftSystem(new AmmoMissileRackSO(3, 0, 0, 180, 360, $ammoMagazine, true));
		$this->addLeftSystem(new MediumPlasma(3, 5, 3, 180, 360));
		$this->addLeftSystem(new MediumPlasma(3, 5, 3, 180, 360));

		$this->addRightSystem(new HeavyPlasma(3, 8, 5, 0, 180));
		$this->addRightSystem(new AmmoMissileRackSO(3, 0, 0, 0, 180, $ammoMagazine, true));
		$this->addRightSystem(new MediumPlasma(3, 5, 3, 0, 180));
		$this->addRightSystem(new MediumPlasma(3, 5, 3, 0, 180));

		$this->addFrontSystem(new Structure( 3, 50));
		$this->addAftSystem(new Structure( 3, 50));
		$this->addLeftSystem(new Structure( 3, 50));
		$this->addRightSystem(new Structure( 3, 50));
		$this->addPrimarySystem(new Structure( 4, 64));
		
		$this->hitChart = array(			
			0=> array(
				8 => "Structure",
				10 => "Cargo Bay",
				12 => "Quarters",
				15 => "Scanner",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				3 => "Heavy Plasma Cannon",
				6 => "Medium Plasma Cannon",
				8 => "Class-SO Missile Rack",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				3 => "Heavy Plasma Cannon",
				6 => "Medium Plasma Cannon",
				8 => "Class-SO Missile Rack",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				3 => "Heavy Plasma Cannon",
				6 => "Medium Plasma Cannon",
				8 => "Class-SO Missile Rack",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				3 => "Heavy Plasma Cannon",
				6 => "Medium Plasma Cannon",
				8 => "Class-SO Missile Rack",
				18 => "Structure",
				20 => "Primary",
			),
		);



		
		}
    }
?>
