<?php
class Lamanna extends OSAT
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 250;
        $this->faction = "Yolu";
//	      $this->variantOf = "";
//        $this->occurence = "";
		$this->phpclass = "Lamanna";
		$this->shipClass = "Lamanna Flayer OSAT";
		$this->imagePath = "img/ships/YoluLashassi.png";
		$this->canvasSize = 80;
		$this->isd = 2238;

		$this->forwardDefense = 7;
		$this->sideDefense = 8;
		
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->accelcost = 0;
		$this->rollcost = 0;
		$this->pivotcost = 0;
		$this->iniativebonus = 12 *5;
		
		$this->addPrimarySystem(new Reactor(6, 9, 0, 0));
		$this->addPrimarySystem(new Scanner(6, 7, 3, 8));
		
		$this->addAftSystem(new Thruster(6, 7, 0, 0, 2));
		
		$this->addFrontSystem(new MolecularFlayer(4, 8, 4, 300, 60));
		$this->addFrontSystem(new MolecularFlayer(4, 8, 4, 300, 60));
		$this->addFrontSystem(new FusionCannon(3, 8, 1, 270, 90));
		$this->addFrontSystem(new FusionCannon(3, 8, 1, 270, 90));	


		//0:primary, 1:front, 2:rear, 3:left, 4:right;

		$this->addPrimarySystem(new Structure(6, 25));

			$this->hitChart = array(
                0=> array(
                        8 => "Structure",
                        11 => "2:Thruster",
						13 => "1:Molecular Flayer",
						15 => "1:Fusion Cannon",
						17 => "Scanner",
                        20 => "Reactor",
                ),
        );

	}

}

?>