<?php
class Galleon extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 450;
        $this->faction = "Raiders";
        $this->phpclass = "Galleon";
        $this->imagePath = "img/ships/galleon.png";
        $this->shipClass = "Galleon";
        $this->occurence = "common";
        $this->fighters = array("normal"=>12);
        
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 1.5;
        $this->turndelaycost = 1.5;
        $this->accelcost = 4;
        $this->rollcost = 4;
        $this->pivotcost = 8;
        $this->iniativebonus = 30;
        
        $this->addPrimarySystem(new Reactor(4, 11, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 4, 5));
        $this->addPrimarySystem(new Engine(4, 14, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(3, 8));
        $this->addPrimarySystem(new Hangar(3, 8));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 4, 4));
        $this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 180, 360));
        $this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 180, 360));
        $this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 180));
        $this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 180));
        $this->addPrimarySystem(new CargoBay(3, 40));

        $this->addFrontSystem(new Thruster(3, 14, 0, 6, 1));
        $this->addFrontSystem(new ParticleCannon(3, 8, 7, 240, 360));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 240, 360));
        $this->addFrontSystem(new MediumPlasma(3, 6, 3, 0, 120));
        $this->addFrontSystem(new ParticleCannon(3, 8, 8, 0, 120));
        $this->addFrontSystem(new CargoBay(3, 60));

        $this->addAftSystem(new Thruster(4, 20, 0, 8, 2));
        $this->addAftSystem(new CargoBay(3, 60));
        $this->addAftSystem(new TwinArray(2, 6, 2, 180, 360));
        $this->addAftSystem(new TwinArray(2, 6, 2, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 40));
        $this->addAftSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 4, 40));
    }
}


?>
