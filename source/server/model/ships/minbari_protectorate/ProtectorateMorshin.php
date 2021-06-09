<?php
class ProtectorateMorshin extends HeavyCombatVessel{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 585;
        $this->faction = "Minbari Protectorate";
        $this->phpclass = "ProtectorateMorshin";
        $this->imagePath = "img/ships/morshin.png";
        $this->shipClass = "Protectorate Morshin Carrier";
        $this->gravitic = true;
        $this->limited = 33;
        $this->fighters = array("normal"=>48);
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        $this->turncost = 0.75;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        $this->iniativebonus = 35;
        $this->isd = 2022;

        $this->addPrimarySystem(new Reactor(5, 20, 0, 2));
        $this->addPrimarySystem(new CnC(5, 24, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 20, 4, 10));
        $this->addPrimarySystem(new Engine(5, 20, 0, 10, 3));
        $this->addPrimarySystem(new Hangar(4, 48));
        $this->addPrimarySystem(new GraviticThruster(4, 11, 0, 4, 3));
        $this->addPrimarySystem(new GraviticThruster(4, 11, 0, 4, 4));

        $this->addFrontSystem(new ElectroPulseGun(2, 6, 3, 240, 0));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 240, 0));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 240, 0));
        $this->addFrontSystem(new ElectroPulseGun(2, 6, 3, 300, 60));
        $this->addFrontSystem(new GraviticThruster(4, 8, 0, 4, 1));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 0, 120));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 0, 120));
        $this->addFrontSystem(new ElectroPulseGun(2, 6, 3, 0, 120));

        $this->addAftSystem(new ElectroPulseGun(2, 6, 3, 180, 300));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));
        $this->addAftSystem(new GraviticThruster(4, 28, 0, 10, 2));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));
        $this->addAftSystem(new ElectroPulseGun(2, 6, 3, 60, 180));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 39));
        $this->addAftSystem(new Structure( 4, 36));
        $this->addPrimarySystem(new Structure( 5, 45));
		
		$this->hitChart = array(
            0=> array(
                    9 => "Structure",
					11 => "Thruster",
					13 => "Scanner",
                    14 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
                    7 => "Fusion Cannon",
                    10 => "Electro-Pulse Gun",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
                    8 => "Fusion Cannon",
                    10 => "Electro-Pulse Gun",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
