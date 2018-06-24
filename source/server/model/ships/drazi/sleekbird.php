<?php
class Sleekbird extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 400;
        $this->faction = "Drazi";
        $this->phpclass = "Sleekbird";
        $this->imagePath = "img/ships/drazi/warbird3.png";
        $this->shipClass = "Sleekbird Assault Cruiser";
	    $this->variantOf = "Warbird Cruiser";
	    $this->isd = 2052;
        $this->canvasSize = 256;
	    
        $this->fighters = array("assault shuttles" => 6); //originally 3 AS and 3 Breaching Pods

        $this->forwardDefense = 13;
        $this->sideDefense = 12;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 40;

        $this->addPrimarySystem(new Reactor(5, 10, 0, 6));
        $this->addPrimarySystem(new CnC(5, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 4, 7));
        $this->addPrimarySystem(new Engine(5, 11, 0, 8, 2));
        $this->addPrimarySystem(new Hangar(4, 7));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 4, 1));
        $this->addPrimarySystem(new Thruster(5, 19, 0, 8, 2));

        $this->addLeftSystem(new StdParticleBeam(3, 4, 1, 240, 60));
        $this->addLeftSystem(new StdParticleBeam(3, 4, 1, 240, 60));
        $this->addLeftSystem(new Thruster(4, 13, 0, 4, 3));

        $this->addRightSystem(new StdParticleBeam(3, 4, 1, 300, 120));
        $this->addRightSystem(new StdParticleBeam(3, 4, 1, 300, 120));
        $this->addRightSystem(new Thruster(4, 13, 0, 4, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(5, 32));
        $this->addLeftSystem(new Structure(4, 40));
        $this->addRightSystem(new Structure(4, 40));
    }
}
?>
