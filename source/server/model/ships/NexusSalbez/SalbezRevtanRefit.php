<?php
class SalbezRevTanRefit extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 215;
		$this->faction = 'Nexus Sal-bez Coalition';
        $this->phpclass = "SalbezRevTanRefit";
        $this->imagePath = "img/ships/Nexus/salbez_revtan3.png";
			$this->canvasSize = 65; //img has 100px per side
        $this->shipClass = "Rev'tan OSAT (2151)";
			$this->variantOf = "Rev'tan OSAT";
			$this->occurence = "common";
		$this->unofficial = true;
		$this->isd = 2151;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));
        $this->addPrimarySystem(new MediumLaser(3, 6, 5, 300, 60));
        $this->addPrimarySystem(new NexusRangedSwarmTorpedo(3, 5, 2, 270, 90));
        $this->addPrimarySystem(new NexusImprovedParticleBeam(2, 3, 1, 0, 360));
        $this->addPrimarySystem(new NexusImprovedParticleBeam(2, 3, 1, 0, 360));
        $this->addPrimarySystem(new NexusRangedSwarmTorpedo(3, 5, 2, 270, 90));
        $this->addPrimarySystem(new MediumLaser(3, 6, 5, 300, 60));
        $this->addPrimarySystem(new Reactor(4, 14, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 7, 3, 5));
        $this->addPrimarySystem(new Thruster(3, 6, 0, 0, 2));
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(4, 36));
		
		$this->hitChart = array(
			0=> array(
				8 => "Structure",
				10 => "Thruster",
				12 => "Ranged Swarm Torpedo",
				14 => "Medium Laser",
				16 => "Improved Particle Beam",
				18 => "Scanner",
				20 => "Reactor",
			),
			1=> array(
				20 => "Primary",
			),
			2=> array(
				20 => "Primary",
			),
        );
    }
}

?>
