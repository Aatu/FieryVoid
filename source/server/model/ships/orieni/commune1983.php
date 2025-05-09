<?php
class Commune1983 extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 400;
        $this->faction = "Orieni Imperium";
        $this->phpclass = "Commune1983";
        $this->imagePath = "img/ships/commune.png";
        $this->shipClass = "Commune Battle Leader (early)";
        $this->variantOf = "Commune Battle Leader";
        $this->isd = 1983;
        $this->limited = 33;
        
        $this->canvasSize = 100;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus = 65;
        
         
        $this->addPrimarySystem(new Reactor(5, 18, 0, 3));
        $this->addPrimarySystem(new CnC(5, 18, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 3, 6));
        $this->addPrimarySystem(new Engine(4, 18, 0, 12, 3));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 5, 4));        
        
        $this->addFrontSystem(new OrieniGatlingRG(1, 4, 1, 240, 120));
        $this->addFrontSystem(new LaserLance(2, 6, 4, 240, 60));
        $this->addFrontSystem(new LaserLance(2, 6, 4, 300, 120));
        $this->addFrontSystem(new OrieniGatlingRG(1, 4, 1, 240, 120));
        $this->addFrontSystem(new HKControlNode(5, 12, 1, 1));       
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
             
        $this->addAftSystem(new OrieniGatlingRG(1, 4, 1, 120, 360)); 
        $this->addAftSystem(new OrieniGatlingRG(1, 4, 1, 0, 240)); 
        $this->addAftSystem(new Thruster(2, 6, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 6, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 6, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 6, 0, 3, 2));   
        
        $this->addPrimarySystem(new Structure(5, 56));
        
        //d20 hit chart
        $this->hitChart = array(
            0=> array(
                8 => "Thruster",
                11 => "Scanner",
                15 => "Engine",
                17 => "Hangar",
                19 => "Reactor",
                20 => "C&C",
            ),
            1=> array(
                5 => "Thruster",
                8 => "Laser Lance",
                10 => "Gatling Railgun",
                12 => "HK Control Node",
                17 => "Structure",
                20 => "Primary",
            ),
            2=> array(
                7 => "Thruster",
                9 => "Gatling Railgun",
                17 => "Structure",
                20 => "Primary",
            ),
        );        
            
    }
}
?>
