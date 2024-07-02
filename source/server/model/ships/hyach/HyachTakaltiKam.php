<?php
class HyachTakaltiKam extends OSAT
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 250;
		$this->faction = "Hyach Gerontocracy";
		$this->phpclass = "HyachTakaltiKam";
		$this->shipClass = "Takalti Kam OSAT";
		$this->imagePath = "img/ships/HyachTakaltiKamOSAT.png";
		$this->canvasSize = 100;
		$this->isd = 2212;

		$this->forwardDefense = 10;
		$this->sideDefense = 10;
		
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->accelcost = 0;
		$this->rollcost = 0;
		$this->pivotcost = 0;
		$this->iniativebonus = 60;

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));		
		$this->addPrimarySystem(new Reactor(4, 9, 0, 0));
		$sensors = new Scanner(4, 9, 2, 7);
			$sensors->markHyach();
			$this->addPrimarySystem($sensors); 
		$this->addAftSystem(new Thruster(4, 6, 0, 0, 2));
		$this->addAftSystem(new Maser(3, 6, 3, 180, 360));
		$this->addFrontSystem(new BlastLaser(4, 10, 5, 270, 90));
		$this->addFrontSystem(new Interdictor(2, 4, 1, 0, 360));
		$this->addFrontSystem(new BlastLaser(4, 10, 5, 270, 90));
		$this->addAftSystem(new Maser(3, 6, 3, 0, 180));

		//0:primary, 1:front, 2:rear, 3:left, 4:right;

		$this->addPrimarySystem(new Structure(5, 35));

			$this->hitChart = array(
                0=> array(
					9 => "Structure",
                    11 => "2:Thruster",
					13 => "1:Blast Laser",
					15 => "2:Maser",
					17 => "Scanner",
                    19 => "Reactor",
					20 => "1:Interdictor",
                ),						
       		);

	}

}

?>