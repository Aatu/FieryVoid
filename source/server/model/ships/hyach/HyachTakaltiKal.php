<?php
class HyachTakaltiKal extends OSAT
{
	public $HyachSpecialists;
	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 750;
		$this->faction = "Hyach Gerontocracy";
		$this->phpclass = "HyachTakaltiKal";
		$this->shipClass = "Takalti Kal Heavy OSAT";
		$this->imagePath = "img/ships/HyachTakaltiKalOSAT.png";
		$this->canvasSize = 100;
		$this->isd = 2230;

		$this->forwardDefense = 12;
		$this->sideDefense = 12;
		
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->accelcost = 0;
		$this->rollcost = 0;
		$this->pivotcost = 0;
		$this->iniativebonus = 60;
	
        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));		
		$this->addPrimarySystem(new Reactor(4, 25, 0, 0));
		$sensors = new Scanner(4, 20, 3, 8);
			$sensors->markHyach();
			$this->addPrimarySystem($sensors); 
		$this->addAftSystem(new Thruster(4, 20, 0, 0, 2));
		$this->addAftSystem(new Interdictor(2, 4, 1, 0, 360));
		$this->addAftSystem(new Maser(3, 6, 3, 180, 360));
		$this->addAftSystem(new Maser(3, 6, 3, 180, 360));
		$this->addFrontSystem(new MediumLaser(3, 6, 7, 300, 60));
		$this->addFrontSystem(new BlastLaser(4, 10, 5, 300, 60));
		$this->addFrontSystem(new SpinalLaser(5, 12, 12, 330, 30));
		$this->addFrontSystem(new BlastLaser(4, 10, 5, 300, 60));
		$this->addFrontSystem(new MediumLaser(3, 6, 7, 300, 60));
		$this->addAftSystem(new Maser(3, 6, 3, 0, 180));
		$this->addAftSystem(new Maser(3, 6, 3, 0, 180));
		$this->addAftSystem(new Interdictor(2, 4, 1, 0, 360));
		$this->addPrimarySystem(new HyachComputer(4, 5, 0, 1));//$armour, $maxhealth, $powerReq, $output			
		$HyachSpecialists = $this->createHyachSpecialists(1); //$specTotal
			$this->addPrimarySystem( $HyachSpecialists );	

		//0:primary, 1:front, 2:rear, 3:left, 4:right;

		$this->addPrimarySystem(new Structure(5, 116));

			$this->hitChart = array(
                0=> array(
					4 => "Structure",
                    6 => "2:Thruster",
					8 => "1:Spinal Laser",
					10 => "1:Medium Laser",
					12 => "1:Blast Laser",
					14 => "2:Maser",
					16 => "Scanner",
                    18 => "Reactor",
					19 => "Computer",
					20 => "2:Interdictor",
                ),								
        	);

	}

}

?>