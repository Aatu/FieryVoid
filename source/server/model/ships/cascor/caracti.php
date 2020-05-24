<?php

class Caracti extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 340;
        $this->faction = "Civilians";
        $this->phpclass = "Caracti";
        $this->imagePath = "img/ships/CascorCalacca.png";
		$this->canvasSize = 200;
        $this->shipClass = "Cascor Caracti Q-Ship";
        $this->isd = 2229;
		
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 99;
        $this->pivotcost = 99;
        $this->iniativebonus = 5;
        $this->fighters = array("ultralight"=>18);
        
        $this->addPrimarySystem(new Reactor(3, 15, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 3, 5));
        $this->addPrimarySystem(new Engine(3, 14, 0, 6, 8));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 2, 3));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 2, 4));
        
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new DualIonBolter(2, 4, 4, 240, 360));
        $this->addFrontSystem(new IonCannon(3, 6, 4, 300, 60));
        $this->addFrontSystem(new Hangar(3, 6));
        $this->addFrontSystem(new IonCannon(3, 6, 4, 300, 60));
        $this->addFrontSystem(new DualIonBolter(2, 4, 4, 0, 120));
        
        $this->addAftSystem(new Thruster(2, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 8, 0, 3, 2));
        $this->addAftSystem(new DualIonBolter(2, 4, 4, 180, 300));
        $this->addAftSystem(new IonCannon(3, 6, 4, 120, 240));
        $this->addAftSystem(new Hangar(3, 6));
        $this->addAftSystem(new DualIonBolter(2, 4, 4, 60, 180));
        
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
                10 => "Ion Cannon",
                18 => "Structure",
                20 => "Primary",
            ),
            2=> array(
                4 => "Thruster",
                6 => "Dual Ion Bolter",
                8 => "Ion Cannon",
                10 => "Hangar",
                18 => "Structure",
                20 => "Primary",
            ),
        );
    }
}

?>
