<?php

class TargathCAM extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 700;
        $this->faction = "Dilgar Imperium";
        $this->phpclass = "TargathCAM";
        $this->imagePath = "img/ships/targath.png";
        $this->shipClass = "Targath-C Attack Cruiser";
        $this->shipSizeClass = 3;
                $this->isd = 2231;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 15;

        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 4;
        
        $this->occurence = "rare";
        $this->variantOf = "Targath Strike Cruiser";

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(40); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 40); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
        $this->enhancementOptionsEnabled[] = 'AMMO_C';//add enhancement options for other missiles - Class-C
        $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-F        
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
        $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
        //$this->enhancementOptionsEnabled[] = 'AMMO_K';//Dilgar were wiped out before Starburst missile was devised
        //$this->enhancementOptionsEnabled[] = 'AMMO_M';//Dilgar were wiped out before Multiwarhead missile was devised
        //$this->enhancementOptionsEnabled[] = 'AMMO_P';//Dilgar were wiped out before Piercing missile was devised     
		
        $this->addPrimarySystem(new Reactor(4, 20, 0, 0));
        $this->addPrimarySystem(new CnC(5, 21, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 4, 9));
        $this->addPrimarySystem(new Engine(5, 11, 0, 6, 3));
        $this->addPrimarySystem(new Hangar(4, 2));

        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new HeavyBolter(3, 10, 6, 300, 60));
        $this->addFrontSystem(new HeavyBolter(3, 10, 6, 300, 60));
		$this->addFrontSystem(new AmmoMissileRackS(3, 0, 0, 300, 60, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new AmmoMissileRackS(3, 0, 0, 300, 60, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new ScatterPulsar(1, 4, 2, 300, 60));
        $this->addFrontSystem(new ScatterPulsar(1, 4, 2, 300, 60));

        $this->addAftSystem(new Thruster(2, 4, 0, 1, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(2, 4, 0, 1, 2));
        $this->addAftSystem(new Engine(4, 7, 0, 4, 3));
        $this->addAftSystem(new MediumBolter(2, 8, 4, 120, 240));
        $this->addAftSystem(new MediumBolter(2, 8, 4, 120, 240));
        $this->addAftSystem(new EnergyPulsar(2, 6, 3, 180, 300));
        $this->addAftSystem(new EnergyPulsar(2, 6, 3, 60, 180));

        $this->addLeftSystem(new Thruster(3, 13, 0, 5, 3));
        $this->addLeftSystem(new QuadPulsar(3, 10, 4, 240, 360));
        $this->addLeftSystem(new ScatterPulsar(1, 4, 2, 180, 300));
        $this->addLeftSystem(new ScatterPulsar(1, 4, 2, 240, 360));

        $this->addRightSystem(new Thruster(3, 13, 0, 5, 4));
        $this->addRightSystem(new QuadPulsar(3, 10, 4, 0, 120));
        $this->addRightSystem(new ScatterPulsar(1, 4, 2, 60, 180));
        $this->addRightSystem(new ScatterPulsar(1, 4, 2, 0, 120));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 42));
        $this->addAftSystem(new Structure( 4, 33));
        $this->addLeftSystem(new Structure( 4, 39));
        $this->addRightSystem(new Structure( 4, 39));
        $this->addPrimarySystem(new Structure( 5, 40));
        
        $this->hitChart = array(
                0=> array(
                    10 => "Structure",
                    13 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
                ),
                1=> array(
                    4 => "Thruster",
                    6 => "Heavy Bolter",
                    8 => "Scatter Pulsar",
                    18 => "Structure",
                    20 => "Primary",
                ),
                2=> array(
                    6 => "Thruster",
                    8 => "Energy Pulsar",
                    10 => "Medium Bolter",
                    11 => "Engine",
                    18 => "Structure",
                    20 => "Primary",
                ),
                3=> array(
                    5 => "Thruster",
                    7 => "Quad Pulsar",
                    10 => "Scatter Pulsar",
                    18 => "Structure",
                    20 => "Primary",
                ),
                4=> array(
                    5 => "Thruster",
                    7 => "Quad Pulsar",
                    10 => "Scatter Pulsar",
                    18 => "Structure",
                    20 => "Primary",
                ),
         );
    }

}

?>
