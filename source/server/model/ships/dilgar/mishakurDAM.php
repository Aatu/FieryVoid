<?php

class MishakurDAM extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 1250;
        $this->faction = "Dilgar";
        $this->phpclass = "MishakurDAM";
        $this->imagePath = "img/ships/mishakur.png";
        $this->shipClass = "Mishakur-D Command Dreadnought";
        $this->shipSizeClass = 3;
        $this->isd = 2231;
                
        $this->occurence = "unique";
        $this->variantOf = "Mishakur Dreadnought";        
        $this->limited = 10;

        $this->forwardDefense = 16;
        $this->sideDefense = 17;

        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 4;

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(80); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 80); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
        $this->enhancementOptionsEnabled[] = 'AMMO_C';//add enhancement options for other missiles - Class-C
        $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-F        
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
        $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
        //$this->enhancementOptionsEnabled[] = 'AMMO_K';//Dilgar were wiped out before Starburst missile was devised
        //$this->enhancementOptionsEnabled[] = 'AMMO_M';//Dilgar were wiped out before Multiwarhead missile was devised
        //$this->enhancementOptionsEnabled[] = 'AMMO_P';//Dilgar were wiped out before Piercing missile was devised     
		
        $this->addPrimarySystem(new Reactor(5, 28, 0, -18));
        $this->addPrimarySystem(new CnC(6, 30, 0, 3));
        $this->addPrimarySystem(new Scanner(5, 20, 4, 10));
        $this->addPrimarySystem(new Engine(5, 18, 0, 9, 3));
        $this->addPrimarySystem(new Hangar(4, 4));
        $this->addPrimarySystem(new JumpEngine(5, 16, 4, 36));

        $this->addFrontSystem(new MediumLaser(3, 6, 5, 300, 60));
        $this->addFrontSystem(new MediumLaser(3, 6, 5, 300, 60));
        $this->addFrontSystem(new Thruster(4, 13, 0, 5, 1));
        $this->addFrontSystem(new Thruster(4, 13, 0, 5, 1));
        $this->addFrontSystem(new ScatterPulsar(2, 4, 2, 240, 120));
        $this->addFrontSystem(new ScatterPulsar(2, 4, 2, 240, 120));
        $this->addFrontSystem(new HeavyBolter(4, 10, 6, 300, 360));
        $this->addFrontSystem(new HeavyBolter(3, 10, 6, 330, 30));
        $this->addFrontSystem(new HeavyBolter(4, 10, 6, 0, 60));
        $this->addFrontSystem(new MassDriver(4, 18, 9, 330, 30));
        $this->addFrontSystem(new MassDriver(4, 18, 9, 330, 30));

        $this->addAftSystem(new ScatterPulsar(2, 4, 2, 120, 360));
        $this->addAftSystem(new HeavyBolter(4, 10, 6, 180, 240));
        $this->addAftSystem(new MediumLaser(3, 6, 5, 120, 240));
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));
        $this->addAftSystem(new Engine(4, 13, 0, 7, 4));
        $this->addAftSystem(new MediumLaser(3, 6, 5, 120, 240));
        $this->addAftSystem(new HeavyBolter(4, 10, 6, 120, 180));
        $this->addAftSystem(new ScatterPulsar(2, 4, 2, 0, 240));

        $this->addLeftSystem(new ScatterPulsar(2, 4, 2, 180, 60));
        $this->addLeftSystem(new QuadPulsar(4, 10, 4, 300, 360));
        $this->addLeftSystem(new HeavyBolter(4, 10, 6, 300, 360));
		$this->addLeftSystem(new AmmoMissileRackS(3, 0, 0, 240, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addLeftSystem(new AmmoMissileRackS(3, 0, 0, 240, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addLeftSystem(new Thruster(4, 18, 0, 7, 3));

        $this->addRightSystem(new ScatterPulsar(2, 4, 2, 300, 180));
        $this->addRightSystem(new QuadPulsar(4, 10, 4, 0, 60));
        $this->addRightSystem(new HeavyBolter(4, 10, 6, 0, 60));
		$this->addRightSystem(new AmmoMissileRackS(3, 0, 0, 240, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addRightSystem(new AmmoMissileRackS(3, 0, 0, 240, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addRightSystem(new Thruster(4, 18, 0, 7, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 54));
        $this->addAftSystem(new Structure( 6, 54));
        $this->addLeftSystem(new Structure( 6, 60));
        $this->addRightSystem(new Structure( 6, 60));
        $this->addPrimarySystem(new Structure( 6, 64));
        
        $this->hitChart = array(
                0=> array(
                    10 => "Structure",
                    12 => "Jump Engine",
                    14 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
                ),
                1=> array(
                    4 => "Thruster",
                    6 => "Mass Driver",
                    9 => "Heavy Bolter",
                    11 => "Scatter Pulsar",
                    13 => "Medium Laser",
                    18 => "Structure",
                    20 => "Primary",
                ),
                2=> array(
                    6 => "Thruster",
                    8 => "Heavy Bolter",
                    10 => "Medium Laser",
                    12 => "Scatter Pulsar",
                    13 => "Engine",
                    18 => "Structure",
                    20 => "Primary",
                ),
                3=> array(
                    4 => "Thruster",
                    6 => "Heavy Bolter",
                    8 => "Quad Pulsar",
                    10 => "Class-S Missile Rack",
                    11 => "Scatter Pulsar",
                    18 => "Structure",
                    20 => "Primary",
                ),
                4=> array(
                    4 => "Thruster",
                    6 => "Heavy Bolter",
                    8 => "Quad Pulsar",
                    10 => "Class-S Missile Rack",
                    11 => "Scatter Pulsar",
                    18 => "Structure",
                    20 => "Primary",
                ),
          );
    }
}

?>
