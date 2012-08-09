<?php
class Varnic extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 580;
        $this->faction = "Narn";
        $this->phpclass = "Varnic";
        $this->imagePath = "img/ships/varnic.png";
        $this->shipClass = "Var'Nic";
        $this->shipSizeClass = 3;
        
        
        $this->forwardDefense = 14;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 10;

        //primary
        $this->addPrimarySystem(new Reactor(5, 18, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 18, 5, 9));
        $this->addPrimarySystem(new Engine(4, 14, 0, 12, 2));
        $this->addPrimarySystem(new JumpEngine(5, 18, 3, 20));
        $this->addPrimarySystem(new Hangar(5, 17));
        
        //front
        $this->addFrontSystem(new HeavyPulse(5, 6, 4, 300, 60));
        
        $this->addFrontSystem(new Thruster(4, 8, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 8, 0, 4, 1));

        //aft
                  


        $this->addAftSystem(new LightPulse(2, 4, 2, 90, 270));
        $this->addAftSystem(new LightPulse(2, 4, 2, 90, 270));

        
        $this->addAftSystem(new Thruster(3, 24, 0, 12, 2));

        
        
        //left
        
        
        $this->addLeftSystem(new TwinArray(3, 6, 2, 240, 60));
        $this->addLeftSystem(new MediumLaser(3, 6, 5, 240, 60));
        $this->addLeftSystem(new MediumLaser(3, 6, 5, 240, 60));
        $this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));
              

        //right
        
        
        $this->addRightSystem(new TwinArray(3, 6, 2, 300, 120));
        $this->addRightSystem(new IonTorpedo(4, 5, 4, 0, 120));
        $this->addRightSystem(new Thruster(4, 15, 0, 5, 4));
        
        
        //structures
        $this->addFrontSystem(new Structure(4, 40));
        $this->addAftSystem(new Structure(4, 28));
        $this->addLeftSystem(new Structure(5, 60));
        $this->addRightSystem(new Structure(4, 39));
        $this->addPrimarySystem(new Structure(5, 36));
        
    }

}



?>
