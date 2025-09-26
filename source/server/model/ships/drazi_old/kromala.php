<?php
class Kromala extends SmallStarBaseFourSections
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 1250;
		$this->base = true;
		$this->smallBase = true; //"small" as in 4 outer sections
		$this->faction = "Drazi Freehold (WotCR)";
		$this->phpclass = "Kromala";
		$this->shipClass = "Kromala Defense Base";
		$this->imagePath = "img/ships/drazi/DraziKromala.png";
		$this->canvasSize = 280;
		$this->fighters = array("light" => 24);
		$this->isd = 2000;

		$this->shipSizeClass = 3;
		$this->Enormous = true;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 20;
		$this->sideDefense = 20;

		$this->addPrimarySystem(new Reactor(5, 26, 0, 0));
//		$this->addPrimarySystem(new ProtectedCnC(5, 40, 0, 0)); //originally 2 systems with structure 20, armor 5 each
		$cnc = new CnC(5, 20, 0, 0);
		$cnc->startArc = 0;
		$cnc->endArc = 360;
		$this->addPrimarySystem($cnc);
		$cnc = new SecondaryCnC(5, 20, 0, 0);//all-around by default
		$this->addPrimarySystem($cnc);
				
		$this->addPrimarySystem(new Scanner(5, 24, 5, 7));
		$this->addPrimarySystem(new Scanner(5, 24, 5, 7));
		$this->addPrimarySystem(new Hangar(5, 28));
		$this->addPrimarySystem(new ParticleCannon(5, 8, 7, 0, 360));
		$this->addPrimarySystem(new ParticleCannon(5, 8, 7, 0, 360));
		$this->addPrimarySystem(new RepeaterGun(5, 6, 4, 0, 360));
		$this->addPrimarySystem(new RepeaterGun(5, 6, 4, 0, 360));

		//$this->addFrontSystem(new CargoBay(4, 20));
		//$this->addFrontSystem(new SubReactorUniversal(4, 25, 0, 0));
		$this->addFrontSystem(new ParticleCannon(4, 8, 7, 270, 90));
		$this->addFrontSystem(new HeavyPlasma(4, 8, 5, 270, 90));
		$this->addFrontSystem(new ParticleCannon(4, 8, 7, 270, 90));
		$this->addFrontSystem(new StdParticleBeam(4, 4, 1, 270, 90));
		$this->addFrontSystem(new StdParticleBeam(4, 4, 1, 270, 90));

			$cargoBay = new CargoBay(4, 20);
			$cargoBay->startArc = 270;
			$cargoBay->endArc = 90;
			$this->addFrontSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 25, 0, 0);
			$subReactor->startArc = 270;
			$subReactor->endArc = 90;
			$this->addFrontSystem($subReactor);

		//$this->addAftSystem(new CargoBay(4, 20));
		//$this->addAftSystem(new SubReactorUniversal(4, 25, 0, 0));
		$this->addAftSystem(new ParticleCannon(4, 8, 7, 90, 270));
		$this->addAftSystem(new HeavyPlasma(4, 8, 5, 90, 270));
		$this->addAftSystem(new ParticleCannon(4, 8, 7, 90, 270));
		$this->addAftSystem(new StdParticleBeam(4, 4, 1, 90, 270));
		$this->addAftSystem(new StdParticleBeam(4, 4, 1, 90, 270));

			$cargoBay = new CargoBay(4, 20);
			$cargoBay->startArc = 90;
			$cargoBay->endArc = 270;
			$this->addAftSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 25, 0, 0);
			$subReactor->startArc = 90;
			$subReactor->endArc = 270;
			$this->addAftSystem($subReactor);

		//$this->addLeftSystem(new CargoBay(4, 20));
		//$this->addLeftSystem(new SubReactorUniversal(4, 25, 0, 0));
		$this->addLeftSystem(new ParticleCannon(4, 8, 7, 180, 360));
		$this->addLeftSystem(new HeavyPlasma(4, 8, 5, 180, 360));
		$this->addLeftSystem(new ParticleCannon(4, 8, 7, 180, 360));
		$this->addLeftSystem(new StdParticleBeam(4, 4, 1, 180, 360));
		$this->addLeftSystem(new StdParticleBeam(4, 4, 1, 180, 360));

			$cargoBay = new CargoBay(4, 20);
			$cargoBay->startArc = 180;
			$cargoBay->endArc = 360;
			$this->addLeftSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 25, 0, 0);
			$subReactor->startArc = 180;
			$subReactor->endArc = 360;
			$this->addLeftSystem($subReactor);

		//$this->addRightSystem(new CargoBay(4, 20));
		//$this->addRightSystem(new SubReactorUniversal(4, 25, 0, 0));
		$this->addRightSystem(new ParticleCannon(4, 8, 7, 0, 180));
		$this->addRightSystem(new HeavyPlasma(4, 8, 5, 0, 180));
		$this->addRightSystem(new ParticleCannon(4, 8, 7, 0, 180));
		$this->addRightSystem(new StdParticleBeam(4, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(4, 4, 1, 0, 180));

			$cargoBay = new CargoBay(4, 20);
			$cargoBay->startArc = 0;
			$cargoBay->endArc = 180;
			$this->addRightSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 25, 0, 0);
			$subReactor->startArc = 0;
			$subReactor->endArc = 180;
			$this->addRightSystem($subReactor);

		/*replaced by TAGed versions!		
		$this->addFrontSystem(new Structure(4, 100));
		$this->addAftSystem(new Structure(4, 100));
		$this->addLeftSystem(new Structure(4, 100));
		$this->addRightSystem(new Structure(4, 100));
		$this->addPrimarySystem(new Structure(5, 120));		
		*/
		$this->addPrimarySystem(new Structure( 5, 120));//needs to be called first for some reason - static call apparently fails for the first time...
		$this->addFrontSystem(Structure::createAsOuter(4, 100, 270,90));
		$this->addAftSystem(Structure::createAsOuter(4, 100, 90, 270));
		$this->addLeftSystem(Structure::createAsOuter(4, 100, 180, 360));
		$this->addRightSystem(Structure::createAsOuter(4, 100, 0, 180));		
		
		$this->hitChart = array(
			0=> array(
				8 => "Structure",
				10 => "Particle Cannon",
				12 => "Repeater Gun",
				14 => "Scanner",
				16 => "Hangar",
				18 => "Reactor",
				20 => "TAG:C&C",
			),
			1=> array(
				2 => "TAG:Heavy Plasma Cannon",
				5 => "TAG:Particle Cannon",
				7 => "TAG:Standard Particle Beam",
				9 => "TAG:Cargo Bay",
				11 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				2 => "TAG:Heavy Plasma Cannon",
				5 => "TAG:Particle Cannon",
				7 => "TAG:Standard Particle Beam",
				9 => "TAG:Cargo Bay",
				11 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				2 => "TAG:Heavy Plasma Cannon",
				5 => "TAG:Particle Cannon",
				7 => "TAG:Standard Particle Beam",
				9 => "TAG:Cargo Bay",
				11 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				2 => "TAG:Heavy Plasma Cannon",
				5 => "TAG:Particle Cannon",
				7 => "TAG:Standard Particle Beam",
				9 => "TAG:Cargo Bay",
				11 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
		);
		
		
	}
}


?>