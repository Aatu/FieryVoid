<?php
class PolarenPrevnoranRefit extends BaseShipNoAft{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 600;
		$this->faction = "Nexus Polaren Confederacy (early)";
        $this->phpclass = "PolarenPrevnoranRefit";
        $this->imagePath = "img/ships/Nexus/polarenOranet.png";
			$this->canvasSize = 165; //img has 200px per side
        $this->shipClass = "Prevnoran Explorer (refit)";
			$this->variantOf = "Oranet Jump Cruiser";
			$this->occurence = "rare";
		$this->limited = 10;
		$this->unofficial = true;
		$this->isd = 2120;
         
        $this->fighters = array("Breaching Pods"=>2); 
		
        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        
        $this->turncost = 0.75;
        $this->turndelaycost = 0.75;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
		$this->iniativebonus = 0;
         
        $this->addPrimarySystem(new Reactor(4, 20, 0, 0));
        $this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new ELINTScanner(4, 12, 6, 6));
        $this->addPrimarySystem(new Engine(4, 16, 0, 8, 3));
		$this->addPrimarySystem(new Hangar(2, 4, 4));
		$this->addPrimarySystem(New JumpEngine(3, 12, 6, 36));
		$this->addPrimarySystem(new Thruster(2, 15, 0, 4, 2));
		$this->addPrimarySystem(new Thruster(2, 15, 0, 4, 2));

        $this->addFrontSystem(new Thruster(2, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 10, 0, 3, 1));
		$this->addFrontSystem(new CargoBay(1, 18));
		$this->addFrontSystem(new CargoBay(1, 18));
		$this->addFrontSystem(new ELINTScanner(4, 12, 6, 4));
		$this->addFrontSystem(new Maser(2, 6, 3, 240, 60));
		$this->addFrontSystem(new Maser(2, 6, 3, 300, 120));
        
		$this->addLeftSystem(new Thruster(2, 15, 0, 4, 3));
		$this->addLeftSystem(new NexusHeavyMaser(3, 7, 4, 240, 360));
		$this->addLeftSystem(new Maser(2, 6, 3, 240, 60));
		$this->addLeftSystem(new NexusSandCaster(1, 4, 2, 180, 360));
		$this->addLeftSystem(new Maser(2, 6, 3, 180, 360));
		$this->addLeftSystem(new LtBlastCannon(2, 4, 1, 180, 360));
		$this->addLeftSystem(new LtBlastCannon(2, 4, 1, 180, 360));
		
		$this->addRightSystem(new Thruster(2, 15, 0, 4, 4));
		$this->addRightSystem(new NexusHeavyMaser(3, 7, 4, 0, 120));
		$this->addRightSystem(new Maser(2, 6, 3, 300, 120));
		$this->addRightSystem(new NexusSandCaster(1, 4, 2, 0, 180));
		$this->addRightSystem(new Maser(2, 6, 3, 0, 180));
		$this->addRightSystem(new LtBlastCannon(2, 4, 1, 0, 180));
		$this->addRightSystem(new LtBlastCannon(2, 4, 1, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 36));
        $this->addLeftSystem(new Structure( 4, 36));
        $this->addRightSystem(new Structure( 4, 36));
        $this->addPrimarySystem(new Structure( 4, 36));
		
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
					10 => "Jump Engine",
                    12 => "Thruster",
					14 => "ELINT Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
					6 => "Maser",
					8 => "ELINT Scanner",
					11 => "Cargo Bay",
					18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    4 => "Thruster",
					6 => "Heavy Maser",
					8 => "Maser",
					10 => "Light Blast Cannon",
					11 => "Sand Caster",
                    18 => "Structure",
                    20 => "Primary",
			),
            4=> array(
                    4 => "Thruster",
					6 => "Heavy Maser",
					7 => "Maser",
					10 => "Light Blast Cannon",
					11 => "Sand Caster",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );

		
    }
}
?>