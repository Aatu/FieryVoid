<?php

class Calacca extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 150;
        $this->faction = "Cascor";
        $this->phpclass = "Calacca";
        $this->imagePath = "img/ships/artemis.png";
        $this->shipClass = "Calacca Freighter";
        $this->isd = 2225;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 99;
        $this->pivotcost = 99;
        $this->iniativebonus = -20;
        
        $this->addPrimarySystem(new Reactor(3, 11, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 2, 3));
        $this->addPrimarySystem(new Engine(3, 14, 0, 6, 9));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 2, 3));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 2, 4));
        
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new DualIonBolter(2, 4, 4, 240, 360));
        $this->addFrontSystem(new Hangar(3, 3));
        $this->addFrontSystem(new DualIonBolter(2, 4, 4, 0, 120));
        $this->addFrontSystem(new CargoBay(3, 20));
        $this->addFrontSystem(new CargoBay(3, 20));
        $this->addFrontSystem(new CargoBay(3, 20));
        $this->addFrontSystem(new CargoBay(3, 20));
        
        $this->addAftSystem(new Thruster(2, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 8, 0, 3, 2));
        $this->addAftSystem(new DualIonBolter(2, 4, 4, 90, 270));
        $this->addAftSystem(new CargoBay(3, 20));
        $this->addAftSystem(new CargoBay(3, 20));
        $this->addAftSystem(new CargoBay(3, 20));
        $this->addAftSystem(new CargoBay(3, 20));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 40));
        $this->addAftSystem(new Structure( 3, 40));
        $this->addPrimarySystem(new Structure( 3, 40));
        
        $this->hitChart = array(
            0=> array(
                6 => "Structure",
                12 => "Thruster",
                14 => "Scanner",
                16 => "Engine",
                19 => "Reactor",
                20 => "C&C",
            ),
            1=> array(
                4 => "Thruster",
                6 => "Dual Ion Bolter",
                8 => "Hangar",
                12 => "Cargo Bay",
                18 => "Structure",
                20 => "Primary",
            ),
            2=> array(
                4 => "Thruster",
                6 => "Dual Ion Bolter",
                11 => "Cargo Bay",
                18 => "Structure",
                20 => "Primary",
            ),
        );
    }
}

?>
