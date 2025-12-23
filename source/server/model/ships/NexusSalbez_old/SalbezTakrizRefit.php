<?php
class SalbezTakrizRefit extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 100;
		$this->faction = 'Nexus Sal-bez Coalition (early)';
        $this->phpclass = "SalbezTakrizRefit";
        $this->imagePath = "img/ships/Nexus/salbez_takriz3.png";
			$this->canvasSize = 65; //img has 100px per side
        $this->shipClass = "Tak'riz OSAT (2085 refit)";
			$this->variantOf = "Tak'riz Early OSAT";
			$this->occurence = "common";
		$this->unofficial = true;
		$this->isd = 2085;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));
        $this->addPrimarySystem(new LaserCutter(2, 6, 4, 270, 90));
        $this->addPrimarySystem(new NexusRangedBoltTorpedo(2, 5, 2, 270, 90));
        $this->addPrimarySystem(new LightParticleBeamShip(1, 2, 1, 180, 360));
        $this->addPrimarySystem(new LightParticleBeamShip(1, 2, 1, 0, 180));
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
				14 => "Laser Cutter",
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
