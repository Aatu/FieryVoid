<?php
class Balsavor extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 430;
        $this->faction = "Centauri (WotCR)";
        $this->phpclass = "Balsavor";
        $this->imagePath = "img/ships/balciron.png";
        $this->shipClass = "Balsavor Gunship";
        $this->occurence = "rare";
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus = 35;
        
         
        $this->addPrimarySystem(new Reactor(5, 15, 0, 0));
        $this->addPrimarySystem(new CnC(5, 14, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 15, 3, 7));
        $this->addPrimarySystem(new Engine(5, 20, 0, 10, 3));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new Thruster(4, 10, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(4, 10, 0, 4, 4));
        
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new TacLaser(3, 5, 3, 300, 60));
        $this->addFrontSystem(new TacLaser(3, 5, 3, 300, 60));
        $this->addFrontSystem(new TacLaser(3, 5, 3, 300, 60));
        $this->addFrontSystem(new TacLaser(3, 5, 3, 300, 60));

        $this->addAftSystem(new Thruster(3, 14, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 14, 0, 3, 2));        
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 52));
        $this->addAftSystem(new Structure( 4, 60));
        $this->addPrimarySystem(new Structure( 4, 40));
        
        
    }

}



?>
