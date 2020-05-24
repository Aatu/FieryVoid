<?php
class Raven extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 310;
        $this->faction = "Raiders";
        $this->phpclass = "Raven";
        $this->imagePath = "img/ships/merlin.png";
        $this->shipClass = "Drazi Raven Light Raider";
        $this->agile = true;
        $this->canvasSize = 100;
        $this->isd = 2042;

        $this->forwardDefense = 11;
        $this->sideDefense = 11;

        $this->turncost = 0.5;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 70;

        $this->addPrimarySystem(new Reactor(4, 12, 0, 2));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 3, 5));
        $this->addPrimarySystem(new Engine(4, 13, 0, 8, 2));
        $this->addPrimarySystem(new Hangar(4, 1));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 4));

        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 240, 0));
        $this->addFrontSystem(new RepeaterGun(3, 6, 4, 300, 60));
        $this->addFrontSystem(new RepeaterGun(3, 6, 4, 300, 60));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 0, 120));

        $this->addAftSystem(new Thruster(4, 12, 0, 8, 2));

        $this->addPrimarySystem(new Structure( 4, 33));
    
            $this->hitChart = array(
        		0=> array(
        				8=> "Thruster",
        				11=> "Scanner",
        				14=> "Engine",
        				15=> "Hangar",
        				18=> "Reactor",
        				20=> "C&C",
        		),
        		1=> array(
        				5=> "Thruster",
        				7=> "Standard Particle Beam",
        				10=> "Repeater Gun",
        				17=> "Structure",
        				20=> "Primary",
        		),
        		2=> array(
        				6=> "Thruster",
        				17=> "Structure",
        				20=> "Primary",
        		),
        );
    
    }
}
?>
