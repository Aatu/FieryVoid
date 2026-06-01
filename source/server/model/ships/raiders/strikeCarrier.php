<?php
class StrikeCarrier extends BaseShip{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 400;
		$this->faction = "Raiders";
		$this->phpclass = "StrikeCarrier";
		$this->imagePath = "img/ships/RaiderStrikeCarrier.png"; //need to change
		$this->shipClass = "Strike Carrier";
		$this->shipSizeClass = 3;
		$this->fighters = array("light"=>24);

		$this->notes = "Generic raider unit.";
		$this->notes .= "<br> ";

		$this->isd = 2247;
        
		$this->forwardDefense = 14;
		$this->sideDefense = 11;

		$this->turncost = 0.5;
		$this->turndelaycost = 0.66;
		$this->accelcost = 3;
		$this->rollcost = 1;
		$this->pivotcost = 3;
		 
		$this->addPrimarySystem(new Reactor(4, 15, 0, 0));
		$this->addPrimarySystem(new CnC(5, 12, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 20, 5, 6));
		$this->addPrimarySystem(new Engine(4, 15, 0, 12, 3));
		$this->addPrimarySystem(new Hangar(5, 2));
		$this->addPrimarySystem(new CargoBay(5, 30));

		$this->addFrontSystem(new Thruster(4, 13, 0, 5, 1));
		$this->addFrontSystem(new Thruster(4, 13, 0, 5, 1));
		$this->addFrontSystem(new MediumPulse(4, 6, 3, 300, 60));
		$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 300, 60));
		$this->addFrontSystem(new MediumPulse(4, 6, 3, 300, 60));

		//External fighter rails (TT layout: 4 x 3-box + 2 x 6-box = 24 boxes,
		//matching the light=>24 declaration — every light fighter rides a rail).
		//All six connect to the FRONT structure block, so a front-structure hit
		//rolls ONE unmodified 1d20 and, on 16-20, destroys ONE entire rail (fighters
		//on it attempt escape). Add-order vs the front Structure below is irrelevant:
		//each rail's structureSystem is resolved in onConstructed, a separate pass
		//that runs AFTER every system is added (BaseShip::onConstructed).
		//Each rail's launch+land budget == its box count (rails launch independently;
		//arg order: $armour, boxes, output, direction 0=forward, 'light').
	
		$this->addFrontSystem(new FighterRail(5, 3, 3, 0, 'light'));
		$this->addFrontSystem(new FighterRail(5, 6, 6, 0, 'light'));
		$this->addFrontSystem(new FighterRail(5, 3, 3, 0, 'light'));
		$this->addFrontSystem(new FighterRail(5, 3, 3, 0, 'light'));
		$this->addFrontSystem(new FighterRail(5, 6, 6, 0, 'light'));		
		$this->addFrontSystem(new FighterRail(5, 3, 3, 0, 'light'));


		$this->addAftSystem(new JumpEngine(4, 11, 4, 16));
		$this->addAftSystem(new Thruster(4, 15, 0, 6, 2));
		$this->addAftSystem(new Thruster(4, 15, 0, 6, 2));
		$this->addAftSystem(new StdParticleBeam(2, 4, 1, 120, 240));
		
		$this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 360));
		$this->addLeftSystem(new MediumPulse(4, 6, 3, 180, 360));
		
		$this->addRightSystem(new Thruster(4, 15, 0, 5, 4));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addRightSystem(new MediumPulse(4, 6, 3, 0, 180));

		//0:primary, 1:front, 2:rear, 3:left, 4:right;
		//Front structure is 78 and INCLUDES the rail boxes — rails are "part of the
		//structure block" (B5W §10.1). The six FighterRail systems above carry
		//CAPACITY only (doCountForCombatValue = false), so they don't add HP/combat
		//value on top of this 78; rail loss comes from the 1d20 rail crit or full
		//destruction of this block, not an independent rail HP track.
		$this->addFrontSystem(new Structure( 5, 78));
		$this->addAftSystem(new Structure( 5, 48));
		$this->addLeftSystem(new Structure( 4, 36));
		$this->addRightSystem(new Structure( 4, 36));
		$this->addPrimarySystem(new Structure( 5, 50));

		$this->hitChart = array(
				0=> array(
						8 => "Structure",
						12 => "Cargo Bay",
						14 => "Scanner",
						15 => "Hangar",
						17 => "Reactor",
						19 => "Engine",
						20 => "C&C",
				),
				1=> array(
						5 => "Thruster",
						7 => "Medium Pulse Cannon",
						8 => "Standard Particle Beam",
						18 => "Structure",
						20 => "Primary",
				),
				2=> array(
						6 => "Thruster",
						7 => "Standard Particle Beam",
						11 => "Jump Engine",
						18 => "Structure",
						20 => "Primary",
				),
				3=> array(
						4 => "Thruster",
						6 => "Medium Pulse Cannon",
						7 => "Standard Particle Beam",
						18 => "Structure",
						20 => "Primary",
				),
				4=> array(
						4 => "Thruster",
						6 => "Medium Pulse Cannon",
						7 => "Standard Particle Beam",
						18 => "Structure",
						20 => "Primary",
				),
		);
	}
}