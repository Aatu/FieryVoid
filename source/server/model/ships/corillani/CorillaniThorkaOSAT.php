<?php
class CorillaniThorkaOSAT extends OSAT
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 225;
		$this->faction = "Corillani Theocracy";
		$this->phpclass = "CorillaniThorkaOSAT";
		$this->shipClass = "Thor'ka Orbital Satellite";
		$this->imagePath = "img/ships/CorillaniThorka.png";	
		$this->canvasSize = 80;
		$this->isd = 2223;
		$this->notes = 'The Corillani have access to Pakmara OSATs';		

		$this->forwardDefense = 11;
		$this->sideDefense = 11;
		
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->accelcost = 0;
		$this->rollcost = 0;
		$this->pivotcost = 0;
		$this->iniativebonus = 10 *5;

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));		
		$this->addPrimarySystem(new Reactor(5, 9, 0, 2));
		$this->addPrimarySystem(new Scanner(4, 7, 2, 5));
		$this->addAftSystem(new Thruster(4, 6, 0, 0, 2));
		$this->addFrontSystem(new RangedFuser(4, 12, 12, 300, 60));
		$this->addFrontSystem(new MediumPlasma(3, 5, 3, 270, 90));
		$this->addFrontSystem(new MediumPlasma(3, 5, 3, 270, 90));
		$this->addAftSystem(new PakmaraPlasmaWeb(2, 4, 2, 0, 180));
		$this->addAftSystem(new PakmaraPlasmaWeb(2, 4, 2, 180, 360));		


		//0:primary, 1:front, 2:rear, 3:left, 4:right;

		$this->addPrimarySystem(new Structure(5, 30));

			$this->hitChart = array(
                0=> array(
                        9 => "Structure",
                        11 => "2:Thruster",
						12 => "1:Ranged Fuser",
						14 => "1:Medium Plasma Cannon",
						16 => "2:Plasma Web",
						18 => "Scanner",
                        20 => "Reactor",
                ),
        );

	}

}

?>
