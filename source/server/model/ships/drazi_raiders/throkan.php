<?php
class Throkan extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 350;
    	$this->faction = "Raiders";
        $this->phpclass = "Throkan";
        $this->imagePath = "img/ships/drazi/kestrel.png";
        $this->shipClass = "Drazi Throkan Corvette";
        $this->agile = true;
        $this->canvasSize = 128;
        $this->isd = 2144;

        $this->forwardDefense = 12;
        $this->sideDefense = 11;

        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus = 70;

        $this->addPrimarySystem(new Reactor(4, 9, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 4, 6));
        $this->addPrimarySystem(new Engine(4, 9, 0, 6, 2));
        $this->addPrimarySystem(new Hangar(3, 1));
        $this->addPrimarySystem(new Thruster(3, 11, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(3, 11, 0, 3, 4));

        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new ParticleCannon(3, 8, 7, 240, 0));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 240, 60));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 300, 120));
        $this->addFrontSystem(new ParticleCannon(3, 8, 7, 0, 120));

        $this->addAftSystem(new Thruster(4, 13, 0, 6, 2));

        $this->addPrimarySystem(new Structure( 4, 48));
        
        $this->hitChart = array(
        		0=> array(
        				8=> "Thruster",
        				11=> "Scanner",
        				14=> "Engine",
        				16=> "Hangar",
        				19=> "Reactor",
        				20=> "C&C",
        		),
        		1=> array(
        				5=> "Thruster",
        				8=> "Particle Cannon",
        				10=> "Standard Particle Beam",
        				17=> "Structure",
        				20=> "Primary",
        		),
        		2=> array(
        				9=> "Thruster",
        				17=> "Structure",
        				20=> "Primary",
        		),
        );
        
    
    
    }

}



?>
