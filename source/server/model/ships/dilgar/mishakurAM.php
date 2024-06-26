<?php

class MishakurAM extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 975;
        $this->faction = "Dilgar Imperium";
        $this->phpclass = "MishakurAM";
        $this->imagePath = "img/ships/mishakur.png";
        $this->shipClass = "Mishakur Dreadnought";
        $this->shipSizeClass = 3;
        $this->isd = 2227;
                
        $this->limited = 10;
        $this->fighters = array("normal"=>12);

        $this->forwardDefense = 16;
        $this->sideDefense = 17;

        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 4;

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
		
        $this->addPrimarySystem(new Reactor(5, 25, 0, 0));
        $this->addPrimarySystem(new CnC(6, 25, 0, 2));
        $this->addPrimarySystem(new Scanner(5, 16, 3, 9));
        $this->addPrimarySystem(new Engine(5, 13, 0, 7, 3));
        $this->addPrimarySystem(new Hangar(4, 14));
        $this->addPrimarySystem(new JumpEngine(4, 16, 4, 36));

        $this->addFrontSystem(new MediumLaser(2, 6, 5, 300, 60));
        $this->addFrontSystem(new MediumLaser(2, 6, 5, 300, 60));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new ScatterPulsar(1, 4, 2, 240, 120));
        $this->addFrontSystem(new ScatterPulsar(1, 4, 2, 240, 120));
        $this->addFrontSystem(new HeavyBolter(3, 10, 6, 300, 360));
        $this->addFrontSystem(new HeavyBolter(3, 10, 6, 0, 60));

        $this->addAftSystem(new ScatterPulsar(1, 4, 2, 120, 360));
        $this->addAftSystem(new HeavyBolter(3, 10, 6, 180, 240));
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
        $this->addAftSystem(new Engine(3, 11, 0, 5, 3));
        $this->addAftSystem(new HeavyBolter(3, 10, 6, 120, 180));
        $this->addAftSystem(new ScatterPulsar(1, 4, 2, 0, 240));

        $this->addLeftSystem(new ScatterPulsar(1, 4, 2, 180, 60));
        $this->addLeftSystem(new QuadPulsar(3, 10, 4, 300, 360));
        $this->addLeftSystem(new PlasmaTorch(1, 4, 2, 240, 360));
        $this->addLeftSystem(new HeavyBolter(3, 10, 6, 300, 360));
		$this->addLeftSystem(new AmmoMissileRackS(2, 0, 0, 240, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addLeftSystem(new Thruster(3, 15, 0, 5, 3));

        $this->addRightSystem(new ScatterPulsar(1, 4, 2, 300, 180));
        $this->addRightSystem(new QuadPulsar(3, 10, 4, 0, 60));
        $this->addRightSystem(new PlasmaTorch(1, 4, 2, 0, 120));
        $this->addRightSystem(new HeavyBolter(3, 10, 6, 0, 60));
		$this->addRightSystem(new AmmoMissileRackS(2, 0, 0, 240, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addRightSystem(new Thruster(3, 15, 0, 5, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 54));
        $this->addAftSystem(new Structure( 5, 54));
        $this->addLeftSystem(new Structure( 5, 60));
        $this->addRightSystem(new Structure( 5, 60));
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
                    6 => "Heavy Bolter",
                    8 => "Scatter Pulsar",
                    10 => "Medium Laser",
                    18 => "Structure",
                    20 => "Primary",
                ),
                2=> array(
                    6 => "Thruster",
                    8 => "Heavy Bolter",
                    10 => "Scatter Pulsar",
                    11 => "Engine",
                    18 => "Structure",
                    20 => "Primary",
                ),
                3=> array(
                    4 => "Thruster",
                    6 => "Heavy Bolter",
                    8 => "Quad Pulsar",
                    9 => "Plasma Torch",
                    10 => "Class-S Missile Rack",
                    11 => "Scatter Pulsar",
                    18 => "Structure",
                    20 => "Primary",
                ),
                4=> array(
                    4 => "Thruster",
                    6 => "Heavy Bolter",
                    8 => "Quad Pulsar",
                    9 => "Plasma Torch",
                    10 => "Class-S Missile Rack",
                    11 => "Scatter Pulsar",
                    18 => "Structure",
                    20 => "Primary",
                ),
          );
    }

}

?>
