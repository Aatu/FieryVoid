<?php
class SalbezTakriz extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 80;
		$this->faction = 'Nexus Sal-bez Coalition (early)';
        $this->phpclass = "SalbezTakriz";
        $this->imagePath = "img/ships/Nexus/salbez_takriz3.png";
			$this->canvasSize = 65; //img has 100px per side
        $this->shipClass = "Tak'riz Early OSAT";
//	    $this->variantOf = "Brixadii Weapons Platform";
//		$this->limited = 33;
		$this->unofficial = true;
		$this->isd = 2027;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));
        $this->addPrimarySystem(new NexusIndustrialLaser(2, 6, 3, 270, 90));
        $this->addPrimarySystem(new NexusRangedBoltTorpedo(2, 5, 2, 270, 90));
        $this->addPrimarySystem(new NexusParticleGrid(1, 3, 1, 180, 360));
        $this->addPrimarySystem(new NexusParticleGrid(1, 3, 1, 0, 180));
        $this->addPrimarySystem(new Reactor(2, 7, 0, 0));
        $this->addPrimarySystem(new Scanner(2, 7, 2, 4));
        $this->addPrimarySystem(new Thruster(2, 6, 0, 0, 2));
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(3, 25));
		
		$this->hitChart = array(
			0=> array(
				8 => "Structure",
				10 => "Thruster",
				12 => "Ranged Bolt Torpedo",
				14 => "Industrial Laser",
				16 => "Particle Grid",
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
