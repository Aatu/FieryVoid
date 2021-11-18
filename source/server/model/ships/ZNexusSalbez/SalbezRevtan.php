<?php
class SalbezRevTan extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 200;
		$this->faction = 'ZNexus Sal-bez';
        $this->phpclass = "SalbezRevTan";
        $this->imagePath = "img/ships/Nexus/salbez_revtan.png";
			$this->canvasSize = 50; //img has 100px per side
        $this->shipClass = "Rev'tan OSAT";
//			$this->variantOf = "Rev'tan OSAT";
//			$this->occurence = "common";
		$this->unofficial = true;
		$this->isd = 2114;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;

        $this->addPrimarySystem(new MediumLaser(3, 6, 5, 300, 60));
        $this->addPrimarySystem(new NexusSwarmTorpedo(3, 5, 2, 270, 90));
        $this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 360));
        $this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 360));
        $this->addPrimarySystem(new NexusSwarmTorpedo(3, 5, 2, 270, 90));
        $this->addPrimarySystem(new MediumLaser(3, 6, 5, 300, 60));
        $this->addPrimarySystem(new Reactor(4, 14, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 7, 3, 5));
        $this->addPrimarySystem(new Thruster(3, 6, 0, 0, 2));
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(4, 36));
		
		$this->hitChart = array(
			0=> array(
				9 => "Structure",
				11 => "Thruster",
				13 => "Swarm Torpedo",
				15 => "Medium Laser",
				16 => "Light Particle Beam",
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
