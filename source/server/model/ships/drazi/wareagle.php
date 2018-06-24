<?php
class Wareagle extends MediumShipLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 400;
	$this->faction = "Drazi";
        $this->phpclass = "Wareagle";
        $this->imagePath = "img/ships/stareagle.png";
        $this->shipClass = "Wareagle Frigate Leader";
        $this->agile = true;
        $this->canvasSize = 128;
        $this->occurence = "uncommon";
	    
        $this->variantOf = "Stareagle Frigate";
	$this->isd = 2229;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 11;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.25;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 1;
	$this->iniativebonus = 75;

        $this->addPrimarySystem(new ParticleCutter(4, 8, 3, 240, 120));
        $this->addPrimarySystem(new Reactor(5, 10, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 3, 6));
        $this->addPrimarySystem(new Engine(5, 11, 0, 8, 2));
	$this->addPrimarySystem(new Hangar(4, 1));
	$this->addPrimarySystem(new Thruster(4, 10, 0, 4, 1));
	$this->addPrimarySystem(new Thruster(5, 14, 0, 8, 2));
		
        $this->addLeftSystem(new Thruster(4, 11, 0, 4, 3));
        $this->addLeftSystem(new StdParticleBeam(4, 4, 1, 240, 60));
        $this->addLeftSystem(new StdParticleBeam(4, 4, 1, 240, 60));
		
        $this->addRightSystem(new Thruster(4, 11, 0, 4, 4));
        $this->addRightSystem(new StdParticleBeam(4, 4, 1, 300, 120));
        $this->addRightSystem(new StdParticleBeam(4, 4, 1, 300, 120));
		
        $this->addPrimarySystem(new Structure( 5, 36));
    }
}
?>
