<?php

class AthraskalaAM extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 600;
        $this->faction = "Dilgar Imperium";
        $this->phpclass = "AthraskalaAM";
        $this->imagePath = "img/ships/athraskala.png";
        $this->shipClass = "Athraskala Heavy Bomber";
        $this->shipSizeClass = 3;
        $this->isd = 2229;

        $this->forwardDefense = 15;
        $this->sideDefense = 16;
        
        $this->limited = 33;

        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(112); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 112); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
        $this->enhancementOptionsEnabled[] = 'AMMO_C';//add enhancement options for other missiles - Class-C
        $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-F        
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
        $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
        //$this->enhancementOptionsEnabled[] = 'AMMO_K';//Dilgar were wiped out before Starburst missile was devised
        //$this->enhancementOptionsEnabled[] = 'AMMO_M';//Dilgar were wiped out before Multiwarhead missile was devised
        //$this->enhancementOptionsEnabled[] = 'AMMO_P';//Dilgar were wiped out before Piercing missile was devised     
		//Bomb Rack has only Basic and Flash missiles available for bomb racks! BUT Athraskala has missile racks as well :) - but ammo magazine limit is for total missiles
		
        $this->addPrimarySystem(new Reactor(5, 13, 0, 4));
        $this->addPrimarySystem(new CnC(5, 13, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 3, 7));
        $this->addPrimarySystem(new Engine(4, 11, 0, 6, 4));
        $this->addPrimarySystem(new Hangar(4, 2));

		$this->addFrontSystem(new AmmoMissileRackS(3, 0, 0, 300, 60, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new AmmoMissileRackS(3, 0, 0, 300, 60, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new MassDriver(4, 18, 9, 330, 30));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
		$this->addFrontSystem(new AmmoBombRack(1, 0, 0, 300, 60, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new AmmoBombRack(1, 0, 0, 300, 60, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base

		$this->addAftSystem(new AmmoMissileRackS(1, 0, 0, 120, 240, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addAftSystem(new AmmoMissileRackS(1, 0, 0, 120, 240, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
        $this->addAftSystem(new Engine(3, 7, 0, 4, 4));
		$this->addAftSystem(new AmmoBombRack(1, 0, 0, 300, 60, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addAftSystem(new AmmoBombRack(1, 0, 0, 300, 60, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base

        $this->addLeftSystem(new Thruster(3, 13, 0, 5, 3));
        $this->addLeftSystem(new ScatterPulsar(1, 4, 2, 180, 360));

        $this->addRightSystem(new Thruster(3, 13, 0, 5, 4));
        $this->addRightSystem(new ScatterPulsar(1, 4, 2, 0, 180));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 36));
        $this->addAftSystem(new Structure( 4, 36));
        $this->addLeftSystem(new Structure( 4, 42));
        $this->addRightSystem(new Structure( 4, 42));
        $this->addPrimarySystem(new Structure( 5, 40));
        
        $this->hitChart = array(
                0=> array(
                    12 => "Structure",
                    14 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
                ),
                1=> array(
                    4 => "Thruster",
                    6 => "Class-S Missile Rack",
                    8 => "Bomb Rack",
                    11 => "Mass Driver",
                    18 => "Structure",
                    20 => "Primary",
                ),
                2=> array(
                    6 => "Thruster",
                    8 => "Class-S Missile Rack",
                    10 => "Bomb Rack",
                    11 => "Engine",
                    18 => "Structure",
                    20 => "Primary",
                ),
                3=> array(
                    5 => "Thruster",
                    7 => "Scatter Pulsar",
                    18 => "Structure",
                    20 => "Primary",
                ),
                4=> array(
                    5 => "Thruster",
                    7 => "Scatter Pulsar",
                    18 => "Structure",
                    20 => "Primary",
                ),
         );
    }

}

?>
