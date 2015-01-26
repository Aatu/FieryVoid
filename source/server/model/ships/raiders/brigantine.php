<?php
class Brigantine extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 450;
        $this->faction = "Raiders";
        $this->phpclass = "Brigantine";
        $this->imagePath = "img/ships/brigantine.png";
        $this->shipClass = "Brigantine";
        $this->occurence = "common";
        $this->fighters = array("normal"=>6);
        
        $this->forwardDefense = 14;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;
        
        $this->addPrimarySystem(new Reactor(3, 15, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 4, 5));
        $this->addPrimarySystem(new Engine(4, 15, 0, 10, 2));
        $this->addPrimarySystem(new Hangar(3, 8));
        $this->addPrimarySystem(new CargoBay(3, 30));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 4, 4));
        $this->addPrimarySystem(new MediumPlasma(3, 5, 3, 0, 360));
        $this->addPrimarySystem(new MediumPlasma(3, 5, 3, 0, 360));

        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new TwinArray(2, 6, 2, 270, 90));
        $this->addFrontSystem(new MediumPulse(3, 6, 3, 240, 360));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 240, 360));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 0, 120));
        $this->addFrontSystem(new MediumPulse(3, 6, 3, 0, 120));
        
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 180, 360));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 0, 180));
        $this->addAftSystem(new TwinArray(2, 6, 2, 90, 270));
        $this->addAftSystem(new Thruster(3, 12, 0, 5, 2));
        $this->addAftSystem(new CargoBay(3, 30));
        $this->addAftSystem(new Thruster(3, 12, 0, 5, 2));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 38));
        $this->addPrimarySystem(new Structure( 4, 42));
        $this->addAftSystem(new Structure( 3, 30));
    }
}


?>
