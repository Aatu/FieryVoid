<?php
class Senschuka extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    $this->pointCost = 325;
    $this->faction = "Dilgar";
        $this->phpclass = "Senschuka";
        $this->imagePath = "img/ships/senschuka.png";
        $this->shipClass = "Senschuka Patrol Ship";
        $this->agile = true;
        $this->canvasSize = 100;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 11;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 65;

        $this->addPrimarySystem(new Reactor(3, 12, 0, 0));
        $this->addPrimarySystem(new CnC(3, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 3, 6));
        $this->addPrimarySystem(new Engine(3, 7, 0, 4, 2));
        $this->addPrimarySystem(new Hangar(2, 2));
        $this->addPrimarySystem(new Thruster(2, 8, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(2, 8, 0, 3, 4));
        $this->addPrimarySystem(new PlasmaTorch(1, 4, 2, 180, 360));
        $this->addPrimarySystem(new PlasmaTorch(1, 4, 2, 0, 180));

        $this->addFrontSystem(new Thruster(2, 10, 0, 4, 1));
        $this->addFrontSystem(new LightBolter(1, 6, 2, 240, 60));
        $this->addFrontSystem(new MediumBolter(2, 8, 4, 300, 60));
        $this->addFrontSystem(new MediumBolter(2, 8, 4, 300, 60));
        $this->addFrontSystem(new LightBolter(1, 6, 2, 300, 120));

        $this->addAftSystem(new Thruster(2, 7, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 7, 0, 3, 2));
        $this->addAftSystem(new Engine(2, 5, 0, 2, 2));
        $this->addAftSystem(new LightBolter(1, 6, 2, 120, 300));
        $this->addAftSystem(new LightBolter(1, 6, 2, 60, 240));

        $this->addPrimarySystem(new Structure( 3, 44));
        
        $this->hitChart = array(
                0=> array(
                    8 => "Thruster",
                    12 => "Plasma Torch",
                    14 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
                ),
                1=> array(
                    4 => "Thruster",
                    6 => "Light Bolter",
                    8 => "Medium Bolter",
                    17 => "Structure",
                    20 => "Primary",
                ),
                2=> array(
                    6 => "Thruster",
                    8 => "Light Bolter",
                    10 => "Engine",
                    17 => "Structure",
                    20 => "Primary",
                ),
          );
    }
}
?>
