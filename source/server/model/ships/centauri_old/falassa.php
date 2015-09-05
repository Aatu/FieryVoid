<?php
class Falassa extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 450;
        $this->faction = "Centauri (WotCR)";
        $this->phpclass = "Falassa";
        $this->imagePath = "img/ships/falenna.png";
        $this->shipClass = "Falassa Escort Carrier";

        $this->fighters = array("light"=>12);

        $this->forwardDefense = 14;
        $this->sideDefense = 15;

        $this->occurence = "uncommon";
        
        $this->turncost = 1;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;        
         
        $this->addPrimarySystem(new Reactor(5, 14, 0, -3));
        $this->addPrimarySystem(new CnC(5, 18, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 15, 3, 6));
        $this->addPrimarySystem(new Engine(5, 20, 0, 10, 3));
        $this->addPrimarySystem(new Thruster(4, 10, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(4, 10, 0, 4, 4));
        
        $this->addFrontSystem(new Thruster(4, 10, 0, 5, 1));
        $this->addFrontSystem(new Hangar(4, 14, 6));
        $this->addFrontSystem(new Thruster(4, 10, 0, 5, 1));
        $this->addFrontSystem(new TacLaser(3, 5, 4, 300, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
        $this->addFrontSystem(new TacLaser(3, 5, 4, 300, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));

        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 120, 300));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 60, 240));  
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 48));
        $this->addAftSystem(new Structure( 4, 42));
        $this->addPrimarySystem(new Structure( 4, 46));
        
        
        
    }

}



?>
