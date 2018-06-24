<?php
class Strikehawk extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 485;
        $this->faction = "Drazi";
        $this->phpclass = "Strikehawk";
        $this->imagePath = "img/ships/drazi/sunhawk4.png";
        $this->shipClass = "Strikehawk Battle Carrier";
        $this->fighters = array("superheavy" => 1);
	    $this->isd = 2220;
	    $this->variantOf = "Sunhawk Battlecruiser"; //not noted on sheet, but fluf says so and it's consistent with name and silhouette

        $this->canvasSize = 256;
        $this->forwardDefense = 13;
        $this->sideDefense = 12;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 40;

        $this->addPrimarySystem(new Reactor(5, 12, 0, 6));
        $this->addPrimarySystem(new CnC(5, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 13, 4, 8));
        $this->addPrimarySystem(new Engine(5, 11, 0, 8, 2));
        $this->addPrimarySystem(new Catapult(4, 6));
        $this->addPrimarySystem(new Hangar(4, 1));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 4, 1));
        $this->addPrimarySystem(new Thruster(5, 19, 0, 8, 2));

        $this->addLeftSystem(new ParticleCannon(4, 8, 7, 240, 0));
        $this->addLeftSystem(new ParticleBlaster(4, 8, 5, 240, 0));
        $this->addLeftSystem(new Thruster(4, 13, 0, 4, 3));
        $this->addLeftSystem(new StdParticleBeam(3, 4, 1, 240, 60));

        $this->addRightSystem(new ParticleCannon(4, 8, 7, 0, 120));
        $this->addRightSystem(new ParticleBlaster(4, 8, 5, 0, 120));
        $this->addRightSystem(new Thruster(4, 13, 0, 4, 4));
        $this->addRightSystem(new StdParticleBeam(3, 4, 1, 300, 120));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(5, 32));
        $this->addLeftSystem(new Structure(4, 40));
        $this->addRightSystem(new Structure(4, 40));
    }
}
?>
