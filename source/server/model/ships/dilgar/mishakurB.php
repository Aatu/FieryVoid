<?php

class MishakurB extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 1000;
        $this->faction = "Dilgar";
        $this->phpclass = "MishakurB";
        $this->imagePath = "img/ships/mishakur.png";
        $this->shipClass = "Mishakur-B Supercarrier";
        $this->shipSizeClass = 3;
        $this->variantOf = "Mishakur Dreadnought";        
        $this->limited = 10;
        $this->isd = 2232;
                
        $this->fighters = array("heavy"=>72); //12 in main hangar, 12 in aft, 24 in each side hangar

        $this->forwardDefense = 16;
        $this->sideDefense = 17;

        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 4;

        $this->occurence = "rare";
        
        $this->addPrimarySystem(new Reactor(5, 25, 0, 4));
        $this->addPrimarySystem(new CnC(6, 25, 0, 2));
        $this->addPrimarySystem(new Scanner(5, 16, 4, 9));
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
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
        $this->addAftSystem(new Engine(3, 11, 0, 5, 3));
        $this->addAftSystem(new Hangar(3, 12));
        $this->addAftSystem(new ScatterPulsar(1, 4, 2, 0, 240));

        $this->addLeftSystem(new ScatterPulsar(1, 4, 2, 180, 60));
        $this->addLeftSystem(new ScatterPulsar(1, 4, 2, 180, 60));
        $this->addLeftSystem(new HeavyBolter(3, 10, 6, 300, 360));
        $this->addLeftSystem(new Hangar(3, 24));
        $this->addLeftSystem(new Thruster(3, 15, 0, 5, 3));

        $this->addRightSystem(new ScatterPulsar(1, 4, 2, 300, 180));
        $this->addRightSystem(new ScatterPulsar(1, 4, 2, 300, 180));
        $this->addRightSystem(new HeavyBolter(3, 10, 6, 0, 60));
        $this->addRightSystem(new Hangar(3, 24));
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
                    17 => "Primary Hangar",
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
                    8 => "Aft Hangar",
                    10 => "Scatter Pulsar",
                    12 => "Engine",
                    18 => "Structure",
                    20 => "Primary",
                ),
                3=> array(
                    4 => "Thruster",
                    6 => "Heavy Bolter",
                    8 => "Port/Stb Hangar",
                    10 => "Scatter Pulsar",
                    18 => "Structure",
                    20 => "Primary",
                ),
                4=> array(
                    4 => "Thruster",
                    6 => "Heavy Bolter",
                    8 => "Port/Stb Hangar",
                    10 => "Scatter Pulsar",
                    18 => "Structure",
                    20 => "Primary",
                ),
          );
    }
}

?>
