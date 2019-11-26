<?php

class Crocti extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 420;
        $this->faction = "Cascor";
        $this->phpclass = "Crocti";
        $this->imagePath = "img/ships/CascorCrocti.png";
        $this->shipClass = "Crocti Patrol Carrier";
        $this->isd = 2210;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.5;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 30;
        $this->fighters = array("heavy"=>12, "ultralight"=>12);
                
        $this->addPrimarySystem(new Reactor(4, 14, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 3, 5));
        $this->addPrimarySystem(new Engine(4, 17, 0, 8, 6));
        $this->addPrimarySystem(new Thruster(4, 18, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(4, 18, 0, 4, 4));
        
        $this->addFrontSystem(new Thruster(4, 18, 0, 4, 1));
        $this->addFrontSystem(new DualIonBolter(2, 4, 4, 240, 60));
        $this->addFrontSystem(new IonCannon(3, 6, 4, 240, 60));
        $this->addFrontSystem(new IonCannon(3, 6, 4, 300, 120));
        $this->addFrontSystem(new DualIonBolter(2, 4, 4, 300, 120));
        
        $this->addAftSystem(new Thruster(4, 15, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 15, 0, 4, 2));
        $this->addAftSystem(new Hangar(4, 10));
        $this->addAftSystem(new Hangar(4, 10));
        $this->addAftSystem(new DualIonBolter(2, 4, 4, 120, 300));
        $this->addAftSystem(new DualIonBolter(2, 4, 4, 60, 240));
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 40));
        $this->addAftSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 4, 48));
        
        $this->hitChart = array(
            0=> array(
                8 => "Structure",
                11 => "Thruster",
                13 => "Scanner",
                16 => "Engine",
                19 => "Reactor",
                20 => "C&C",
            ),
            1=> array(
                6 => "Thruster",
                8 => "Ion Cannon",
                10 => "Dual Ion Bolter",
                18 => "Structure",
                20 => "Primary",
            ),
            2=> array(
                6 => "Thruster",
                10 => "Hangar",
                12 => "Dual Ion Bolter",
                18 => "Structure",
                20 => "Primary",
            ),
        );
    }
}

?>
