<?php
class Lacanna extends OSAT
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 175;
        $this->faction = "Yolu";
	      	$this->variantOf = "Lashanna Agitator OSAT";
	       	$this->occurence = "common";
		$this->phpclass = "Lacanna";
		$this->shipClass = "Lacanna Cutter OSAT";
		$this->imagePath = "img/ships/YoluLashanna.png";
		$this->canvasSize = 80;
		$this->isd = 1315;

		$this->forwardDefense = 7;
		$this->sideDefense = 8;
		
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->accelcost = 0;
		$this->rollcost = 0;
		$this->pivotcost = 0;
		$this->iniativebonus = 12 *5;
		
		$this->addPrimarySystem(new Reactor(6, 9, 0, 0));
		$this->addPrimarySystem(new Scanner(6, 7, 3, 7));
		
		$this->addAftSystem(new Thruster(6, 7, 0, 0, 2));
		
		$this->addFrontSystem(new FusionCutter(4, 7, 4, 300, 60));
		$this->addFrontSystem(new FusionCutter(4, 7, 4, 300, 60));
		$this->addFrontSystem(new FusionCutter(4, 7, 4, 300, 60));		

		//0:primary, 1:front, 2:rear, 3:left, 4:right;

		$this->addPrimarySystem(new Structure(6, 25));

			$this->hitChart = array(
                0=> array(
                        8 => "Structure",
                        11 => "2:Thruster",
						15 => "1:Fusion Cutter",					
						17 => "Scanner",
                        20 => "Reactor",
                ),
        );

	}

}

?>