<?php
class Leevan extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 340;
        $this->faction = "Centauri (WotCR)";
        $this->phpclass = "Leevan";
        $this->imagePath = "img/ships/navask.png";
        $this->shipClass = "Leevan Mine Sweeper";        

        $this->forwardDefense = 13;
        $this->sideDefense = 13;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;

        $this->occurence = "uncommon";
        
         
        $this->addPrimarySystem(new Reactor(5, 15, 0, 0));
        $this->addPrimarySystem(new CnC(6, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 15, 3, 6));
        $this->addPrimarySystem(new Engine(5, 16, 0, 9, 2));
        $this->addPrimarySystem(new Hangar(4, 5));
        $this->addPrimarySystem(new Thruster(4, 10, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(4, 10, 0, 5, 4));        
        
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));        
        

        $this->addFrontSystem(new LightParticleBeamShip(2, 6, 1, 240, 120));
        $this->addFrontSystem(new LightParticleBeamShip(2, 6, 1, 240, 120));
        $this->addFrontSystem(new ParticleProjector(2, 6, 1, 240, 60));
        $this->addFrontSystem(new ParticleProjector(2, 6, 1, 270, 90));
        $this->addFrontSystem(new ParticleProjector(2, 6, 1, 300, 120));
        
        $this->addAftSystem(new Thruster(2, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 9, 0, 3, 2));
        $this->addAftSystem(new LightParticleBeamShip(2, 6, 1, 240, 360));
        $this->addAftSystem(new LightParticleBeamShip(2, 6, 1, 0, 240));
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 60));
        $this->addAftSystem(new Structure( 4, 60));
        $this->addPrimarySystem(new Structure( 6, 40));
        
        
    }

}



?>
