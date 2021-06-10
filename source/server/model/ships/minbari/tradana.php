<?php
class Tradana extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 575;
		$this->faction = "Minbari";
        $this->phpclass = "Tradana";
        $this->imagePath = "img/ships/tradana.png";
        $this->shipClass = "Tradana";
        $this->canvasSize = 100;
        $this->gravitic = true;
        $this->occurence = "rare";
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
		$this->iniativebonus = 65;
		$this->isd = 2266;
		$this->variantOf = "Torotha";

        $this->addPrimarySystem(new Reactor(5, 18, 0, 0));
        $this->addPrimarySystem(new CnC(6, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 4, 9));
        $this->addPrimarySystem(new Engine(5, 14, 0, 8, 2));
	$this->addPrimarySystem(new Hangar(5, 2));
	$this->addPrimarySystem(new GraviticThruster(4, 10, 0, 4, 3));
	$this->addPrimarySystem(new GraviticThruster(4, 10, 0, 4, 4));
        $this->addPrimarySystem(new Jammer(4, 8, 5));

        $this->addFrontSystem(new GraviticThruster(4, 12, 0, 5, 1));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 240, 360));
        $this->addFrontSystem(new NeutronLaser(3, 10, 6, 240, 360));
        $this->addFrontSystem(new ElectroPulseGun(2, 6, 3, 240, 360));
        $this->addFrontSystem(new ElectroPulseGun(2, 6, 3, 0, 120));
        $this->addFrontSystem(new NeutronLaser(3, 10, 6, 0, 120));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 0, 120));

        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));
        $this->addAftSystem(new ElectroPulseGun(2, 6, 3, 120, 240));
        $this->addAftSystem(new GraviticThruster(4, 21, 0, 8, 2));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));

        $this->addPrimarySystem(new Structure( 6, 60));
		
				$this->hitChart = array(
            0=> array(
					8 => "Thruster",
                    10 => "Jammer",
					12 => "Scanner",
                    14 => "Engine",
                    16 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    6 => "Thruster",
					9 => "Neutron Laser",
					11 => "Fusion Cannon",
					13 => "Electro-Pulse Gun",
                    17 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
					7 => "Electro-Pulse Gun",
                    10 => "Fusion Cannon",
                    17 => "Structure",
                    20 => "Primary",
            ),
        );
    }

}
?>
