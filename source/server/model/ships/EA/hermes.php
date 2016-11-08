<?php
class Hermes extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 420;
        $this->faction = "EA";
        $this->phpclass = "Hermes";
        $this->imagePath = "img/ships/hermes.png";
        $this->shipClass = "Hermes Priority Transport (Beta)";
	    $this->notes = 'Thunderbolt capable.';
        $this->isd = 2168;
        
        
        $this->forwardDefense = 14;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;
        
         
        $this->addPrimarySystem(new Reactor(4, 12, 0, 0));
        $this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 4, 5));
        $this->addPrimarySystem(new Engine(4, 15, 0, 10, 2));
        $this->addPrimarySystem(new Hangar(4, 10));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 4, 4));        
        $this->addPrimarySystem(new SMissileRack(3, 6, 0, 0, 360));
        $this->addPrimarySystem(new SMissileRack(3, 6, 0, 0, 360));
        
        $this->addFrontSystem(new InterceptorMkI(2, 4, 1, 270, 90));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 240, 360));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 0, 120));

        
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 180, 360));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 0, 180));
        $this->addAftSystem(new Thruster(3, 12, 0, 5, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 5, 2));        
        $this->addAftSystem(new InterceptorMkI(2, 4, 1, 90, 270));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 44));
        $this->addAftSystem(new Structure( 4, 45));
        $this->addPrimarySystem(new Structure( 6, 36));
        
        $this->hitChart = array(
                0=> array(
                        6 => "Structure",
                        8 => "Cargo",
                        10 => "Thruster",
                        12 => "Class-S Missile Rack",
                        14 => "Scanner",
                        16 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        6 => "Standard Particle Beam",
                        8 => "Interceptor I",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        8 => "Standard Particle Beam",
                        10 => "Interceptor I",
                        12 => "Jump Engine",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );
        
    }

}



?>
