<?php
class HyachTakaltiKal extends OSAT
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 750;
		$this->faction = "Hyach Gerontocracy";
		$this->phpclass = "HyachTakaltiKal";
		$this->shipClass = "Takalti Kal Heavy OSAT";
		$this->imagePath = "img/ships/OrieniSkywatchOSAT.png";
		$this->canvasSize = 150;
		$this->isd = 2230;

		$this->forwardDefense = 12;
		$this->sideDefense = 12;
		
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->accelcost = 0;
		$this->rollcost = 0;
		$this->pivotcost = 0;
		$this->iniativebonus = 60;
		
		$this->addPrimarySystem(new Reactor(4, 25, 0, 0));
		$sensors = new Scanner(4, 20, 3, 8);
			$sensors->markHyach();
			$this->addPrimarySystem($sensors); 
		$this->addPrimarySystem(new Thruster(4, 20, 0, 0, 2));
		$this->addPrimarySystem(new Interdictor(2, 4, 1, 0, 360));
		$this->addPrimarySystem(new Maser(3, 6, 3, 180, 360));
		$this->addPrimarySystem(new Maser(3, 6, 3, 180, 360));
		$this->addPrimarySystem(new MediumLaser(3, 6, 7, 300, 60));
		$this->addPrimarySystem(new BlastLaser(4, 10, 5, 300, 60));
		$this->addPrimarySystem(new SpinalLaser(5, 12, 12, 330, 30));
		$this->addPrimarySystem(new BlastLaser(4, 10, 5, 300, 60));
		$this->addPrimarySystem(new MediumLaser(3, 6, 7, 300, 60));
		$this->addPrimarySystem(new Maser(3, 6, 3, 0, 180));
		$this->addPrimarySystem(new Maser(3, 6, 3, 0, 180));
		$this->addPrimarySystem(new Interdictor(2, 4, 1, 0, 360));
		$this->addPrimarySystem(new HyachComputer(4, 5, 0, 1));//$armour, $maxhealth, $powerReq, $output			


		//0:primary, 1:front, 2:rear, 3:left, 4:right;

		$this->addPrimarySystem(new Structure(5, 116));

			$this->hitChart = array(
                0=> array(
					4 => "Structure",
                    6 => "Thruster",
					8 => "Spinal Laser",
					10 => "Medium Laser",
					12 => "Blast Laser",
					14 => "Maser",
					16 => "Scanner",
                    18 => "Reactor",
					19 => "Computer",
					20 => "Interdictor",
                ),
				1=> array(
					20 => "Primary",
				),
				2=> array(
					20 => "Primary",
				),								
        );

	}

}

?>