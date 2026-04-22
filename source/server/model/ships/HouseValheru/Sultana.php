<?php
class Sultana extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 525;
        $this->faction = "House Valheru";
        $this->phpclass = "Sultana";
        $this->imagePath = "img/ships/Sultana.png";
        $this->shipClass = "Sultana Support Destroyer";
        $this->isd = 2218;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 0.50;
        $this->turndelaycost = 0.50;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;
         
        $this->addPrimarySystem(new Reactor(6, 15, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 15, 5, 8));
        $this->addPrimarySystem(new Engine(5, 15, 0, 10, 2));
        $this->addPrimarySystem(new Hangar(5, 1));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 4));
        
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Plasmastream(4, 10, 5, 240, 360));
        $this->addFrontSystem(new Dualplasmastream(4, 10, 10, 300, 60));
        $this->addFrontSystem(new Plasmastream(4, 10, 5, 0, 120));
        
        $this->addAftSystem(new Thruster(4, 14, 0, 5, 2));
        $this->addAftSystem(new Thruster(4, 14, 0, 5, 2));
        $this->addAftSystem(new TwinArray(3, 6, 2, 180, 360));
        $this->addAftSystem(new TwinArray(3, 6, 2, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 60));
        $this->addAftSystem(new Structure( 4, 60));
        $this->addPrimarySystem(new Structure( 6, 40));
        
        $this->hitChart = array(
                0=> array(
                    6 => "Structure",
                    10 => "Thruster",
                    13 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
                ),
                1=> array(
                    3 => "Thruster",
                    6 => "Dual Plasma Stream",
                    9 => "Plasma Stream",
                    18 => "Structure",
                    20 => "Primary",
                ),
                2=> array(
                    6 => "Thruster",
                    8 => "Twin Array",
                    18 => "Structure",
                    20 => "Primary",
			),
		); 
    }

}



?>
