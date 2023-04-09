<?php
class AttackFrigate extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 325;
		$this->faction = "Raiders";
		$this->phpclass = "AttackFrigate";
		$this->imagePath = "img/ships/RaiderShokanAttackFrigate.png";
		$this->shipClass = "Shokan Attack Frigate";
		$this->canvasSize = 100;

		$this->notes = 'Used only by Brakiri Shokan Privateers';
		$this->isd = 2233;
		
		$this->forwardDefense = 11;
		$this->sideDefense = 13;

		$this->turncost = 0.5;
		$this->turndelaycost = 0.5;
		$this->accelcost = 3;
		$this->rollcost = 2;
		$this->pivotcost = 2;
		$this->iniativebonus = 12 * 5;

		$this->gravitic = true;
		
		$this->enhancementOptionsDisabled[] = 'IMPR_REA';
		$this->enhancementOptionsDisabled[] = 'IMPR_ENG';		

		$reactor = new Reactor(4, 12, 0, 0);
			$reactor->markPowerFlux();
			$this->addPrimarySystem($reactor);
		$this->addPrimarySystem(new CnC(6, 8, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 9, 5, 6));
		$engine = new Engine(4, 9, 0, 10, 3);
			$engine->markEngineFlux();
			$this->addPrimarySystem($engine);
		$this->addPrimarySystem(new GraviticThruster(4, 10, 0, 5, 3));
		$this->addPrimarySystem(new GraviticThruster(4, 10, 0, 5, 4));
		$this->addPrimarySystem(new StdParticleBeam(4, 4, 1, 180, 360));
		$this->addPrimarySystem(new StdParticleBeam(4, 4, 1, 0, 360));
		

        $this->addFrontSystem(new MediumBolter(4, 8, 4, 240, 360));
        $this->addFrontSystem(new MediumBolter(4, 8, 4, 0, 120));   ;
		$this->addFrontSystem(new GraviticThruster(4, 8, 0, 2, 1));
		$this->addFrontSystem(new GraviticThruster(4, 8, 0, 2, 1));

		$this->addAftSystem(new GraviticThruster(4, 10, 0, 5, 2));
		$this->addAftSystem(new GraviticThruster(4, 10, 0, 5, 2));
		$this->addAftSystem(new Hangar(3, 1));

		$this->addPrimarySystem(new Structure( 4, 50));
		
		$this->hitChart = array(
			0=> array(
					8 => "Thruster",
					11 => "Scanner",
					15 => "Engine",
					18 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					8 => "Medium Bolter",
					10 => "Standard Particle Beam",
					17 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Hangar",
					17 => "Structure",
					20 => "Primary",
			),
		);
		
    }
}




?>
