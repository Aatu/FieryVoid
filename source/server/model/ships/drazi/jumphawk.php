<?php
class Jumphawk extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 500;
	$this->faction = "Drazi";
        $this->phpclass = "Jumphawk";
        $this->imagePath = "img/ships/drazi/sunhawk3.png";
        $this->shipClass = "Jumphawk Command Cruiser";
        $this->occurence = "uncommon";
	    $this->variantOf = "Sunhawk Battlecruiser";
	    $this->isd = 2230;
        $this->canvasSize = 256;

        $this->forwardDefense = 14;
        $this->sideDefense = 13;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 45;

        $this->addPrimarySystem(new Reactor(5, 15, 0, 2));
        $this->addPrimarySystem(new CnC(6, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 13, 4, 8));
        $this->addPrimarySystem(new Engine(5, 11, 0, 10, 2));
        $this->addPrimarySystem(new JumpEngine(4, 16, 4, 0));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new StdParticleBeam(3, 4, 1, 300, 60));
        $this->addPrimarySystem(new Thruster(4, 15, 0, 5, 1));
        $this->addPrimarySystem(new Thruster(5, 21, 0, 10, 2));

        $this->addLeftSystem(new ParticleCannon(4, 8, 7, 240, 0));
        $this->addLeftSystem(new ParticleBlaster(4, 8, 5, 240, 0));
        $this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));
        $this->addLeftSystem(new StdParticleBeam(3, 4, 1, 240, 60));

        $this->addRightSystem(new ParticleCannon(4, 8, 7, 0, 120));
        $this->addRightSystem(new ParticleBlaster(4, 8, 5, 0, 120));
        $this->addRightSystem(new Thruster(4, 15, 0, 5, 4));
        $this->addRightSystem(new StdParticleBeam(3, 4, 1, 300, 120));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(6, 36));
        $this->addLeftSystem(new Structure(5, 44));
        $this->addRightSystem(new Structure(5, 44));
    }
}
?>
