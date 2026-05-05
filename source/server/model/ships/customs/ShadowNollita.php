<?php
class ShadowNollita extends MediumShip
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 800;
		$this->faction = "Custom Ships";
		$this->phpclass = "ShadowNollita";
		$this->imagePath = "img/ships/ShadowNollita.png";
		$this->shipClass = "Shadow Nollita Frigate";
		$this->canvasSize = 100;
		$this->isd = 2262;
		$this->notes = 'Defenders of Corrilan (DoC)';
		$this->notes .= "<br>Just a joke";
        $this->agile = true;				
		$this->unofficial = true; 

		$this->enhancementOptionsDisabled[] = 'SHAD_DIFF'; //no diffuser upgrades for EA ships - they don't have know how to tabper with Shadow systems to that extent!
		$this->advancedArmor = true;   

		$this->forwardDefense = 12;
		$this->sideDefense = 13;

		$this->turncost = 0.5;
		$this->turndelaycost = 0.5;
		$this->accelcost = 2;
		$this->rollcost = 1;
		$this->pivotcost = 1;
		$this->iniativebonus = 13 *5;

		$diffuserPort = new EnergyDiffuser(4, 9, 2, 180, 360);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(5,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(5,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
		$tendril=new DiffuserTendril(5,'L');//absorbtion capacity,side
		$diffuserPort->addTendril($tendril);
		$this->addLeftSystem($tendril);
        $this->addPrimarySystem($diffuserPort);		

		$diffuserStbd = new EnergyDiffuser(4, 9, 2, 0, 180);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
		$tendril=new DiffuserTendril(5,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(5,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
		$tendril=new DiffuserTendril(5,'R');//absorbtion capacity,side
		$diffuserStbd->addTendril($tendril);
		$this->addRightSystem($tendril);
        $this->addPrimarySystem($diffuserStbd);

		$this->addPrimarySystem(new Reactor(3, 10, 0, 0));
		$this->addPrimarySystem(new CnC(4, 9, 0, 0));
		$this->addPrimarySystem(new Scanner(3, 8, 3, 6));
		$this->addPrimarySystem(new Engine(3, 11, 0, 8, 2));
		$this->addPrimarySystem(new Hangar(1, 1));	
		$this->addPrimarySystem(new Thruster(3, 4, 0, 2, 3));
		$this->addPrimarySystem(new Thruster(3, 4, 0, 2, 3));
		$this->addPrimarySystem(new Thruster(3, 4, 0, 2, 4));
		$this->addPrimarySystem(new Thruster(3, 4, 0, 2, 4));						

		$this->addFrontSystem(new PhasingPulseCannonH(5, 0, 0, 240, 60));
		$this->addFrontSystem(new MultiphasedCutterL(2, 0, 0, 270, 90));		
		$this->addFrontSystem(new PhasingPulseCannonH(5, 0, 0, 300, 120));
		$this->addFrontSystem(new Thruster(3, 5, 0, 3, 1));
		$this->addFrontSystem(new Thruster(3, 5, 0, 3, 1));

		$this->addAftSystem(new MultiphasedCutterL(2, 0, 0, 180, 360));
		$this->addAftSystem(new MultiphasedCutterL(2, 0, 0, 90, 270));
		$this->addAftSystem(new MultiphasedCutterL(2, 0, 0, 0, 180));
		$this->addAftSystem(new Thruster(3, 8, 0, 4, 2));
		$this->addAftSystem(new Thruster(3, 8, 0, 4, 2));

		$this->addPrimarySystem(new Structure(4, 60));

			$this->hitChart = array(
					0=> array(
						8 => "Thruster",
						10 => "Energy Diffuser",
						12 => "Scanner",
						15 => "Engine",
						16 => "Hangar",
						18 => "Reactor",
						20 => "C&C",
					),
					1=> array(
						6 => "Thruster",
						8 => "Heavy Phasing Pulse Cannon",
						10 => "Light Multiphased Cutter",
						17 => "Structure",
						20 => "Primary",
					),
					2=> array(
						6 => "Thruster",
						10 => "Light Multiphased Cutter",
						17 => "Structure",
						20 => "Primary",
					),
			);
	}
}



?>