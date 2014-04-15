<?php
class Dagkar extends MediumShip{
    
    function __construct($id, $userid, $name,  $movement){
        parent::__construct($id, $userid, $name,  $movement);
        
        $this->pointCost = 750;
        $this->faction = "Narn";
        $this->phpclass = "Dagkar";
        $this->imagePath = "img/ships/dagkar.png";
        $this->shipClass = "Dag'Kar";
        $this->limited = 33;

        $this->forwardDefense = 14;
        $this->sideDefense = 14;
        
        $this->turncost = 0.50;
        $this->turndelaycost = 0.50;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        
        $this->iniativebonus = 60;

        
        $this->addPrimarySystem(new Reactor(4, 18, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 15, 3, 6));
        $this->addPrimarySystem(new Engine(4, 16, 0, 10, 3));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 5, 4));
        

        
        //front
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));

        $this->addFrontSystem(new EnergyMine(4, 5, 4, 300, 60));
        $this->addFrontSystem(new EnergyMine(4, 5, 4, 300, 60));
        $this->addFrontSystem(new EnergyMine(4, 5, 4, 300, 60));
        $this->addFrontSystem(new EnergyMine(4, 5, 4, 300, 60));
        $this->addFrontSystem(new EnergyMine(4, 5, 4, 300, 60));
        $this->addFrontSystem(new EnergyMine(4, 5, 4, 300, 60));
        $this->addFrontSystem(new IonTorpedo(4, 5, 4, 300, 60));
        $this->addFrontSystem(new IonTorpedo(4, 5, 4, 300, 60));
        $this->addFrontSystem(new IonTorpedo(4, 5, 4, 300, 60));
        $this->addFrontSystem(new IonTorpedo(4, 5, 4, 300, 60));
        $this->addFrontSystem(new IonTorpedo(4, 5, 4, 300, 60));
        $this->addFrontSystem(new IonTorpedo(4, 5, 4, 300, 60));
        
        //aft

        $this->addAftSystem(new Thruster(4, 10, 0, 5, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 5, 2));
        

        //structures
        $this->addPrimarySystem(new Structure(4, 55));
        
    }

}
