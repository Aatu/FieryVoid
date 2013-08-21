<?php
class Vulture extends BaseShipNoAft{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 515;
	$this->faction = "Drazi";
        $this->phpclass = "Vulture";
        $this->imagePath = "img/ships/vulture.png";
        $this->shipClass = "Hunter Vulture Raider";
        $this->fighters = array("light" => 12);
        $this->limited = 33;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 10;

        $this->addPrimarySystem(new Reactor(5, 20, 0, 1));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 6, 7));
        $this->addPrimarySystem(new Engine(5, 20, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(4, 14));
        $this->addPrimarySystem(new JumpEngine(5, 10, 5, 34));
        $this->addPrimarySystem(new Thruster(5, 21, 0, 8, 2));

        $this->addFrontSystem(new ParticleCannon(3, 8, 7, 300, 60));
        $this->addFrontSystem(new RepeaterGun(3, 6, 4, 300, 60));
        $this->addFrontSystem(new ParticleCannon(3, 8, 7, 300, 60));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        
        $this->addLeftSystem(new StdParticleBeam(2, 4, 1, 240, 60));
        $this->addLeftSystem(new LightParticleCannon(2, 6, 5, 240, 60));
        $this->addLeftSystem(new CargoBay(2, 10));
        $this->addLeftSystem(new Thruster(4, 16, 0, 4, 3));

        $this->addRightSystem(new LightParticleCannon(2, 6, 5, 300, 120));
        $this->addRightSystem(new StdParticleBeam(2, 4, 1, 300, 120));
        $this->addRightSystem(new CargoBay(2, 10));
        $this->addRightSystem(new Thruster(4, 16, 0, 4, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(4, 44));
        $this->addLeftSystem(new Structure(4, 40));
        $this->addRightSystem(new Structure(4, 40));
        $this->addFrontSystem(new Structure(4, 40));
    }
}
?>