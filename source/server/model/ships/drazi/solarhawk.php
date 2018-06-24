<?php
class Solarhawk extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 525;
	$this->faction = "Drazi";
        $this->phpclass = "Solarhawk";
        $this->imagePath = "img/ships/drazi/sunhawk6.png";
        $this->shipClass = "Solarhawk Battlecruiser";
        $this->occurence = "rare";
	    $this->variantOf = "Sunhawk Battlecruiser";
	    $this->isd = 2258;
        $this->canvasSize = 256;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 13;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 40;

        $this->addPrimarySystem(new Reactor(5, 14, 0, 6));
        $this->addPrimarySystem(new CnC(5, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 13, 4, 8));
        $this->addPrimarySystem(new Engine(5, 11, 0, 10, 2));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new StdParticleBeam(4, 4, 1, 270, 90));
        $this->addPrimarySystem(new Thruster(4, 15, 0, 5, 1));
        $this->addPrimarySystem(new Thruster(5, 21, 0, 10, 2));

        $this->addLeftSystem(new SolarCannon(4, 7, 3, 240, 0));
        $this->addLeftSystem(new SolarCannon(4, 7, 3, 240, 0));
        $this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));

        $this->addRightSystem(new SolarCannon(4, 7, 3, 0, 120));
        $this->addRightSystem(new SolarCannon(4, 7, 3, 0, 120));
        $this->addRightSystem(new Thruster(4, 15, 0, 5, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(6, 36));
        $this->addLeftSystem(new Structure(5, 44));
        $this->addRightSystem(new Structure(5, 44));
    }
}
?>
