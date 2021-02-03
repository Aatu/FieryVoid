<?php
class SshelathVilindras extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 550;
	$this->faction = "ZEscalation Sshel'ath Alliance";
        $this->phpclass = "SshelathVilindras";
        $this->imagePath = "img/ships/EscalationWars/SshelathVipindra.png";
        $this->shipClass = "Vilindras Heavy Cruiser";
			$this->variantOf = "Vipindra Heavy Cruiser";
			$this->occurence = "uncommon";		
        $this->shipSizeClass = 3;
		$this->canvasSize = 150; //img has 200px per side
		$this->unofficial = true;

		$this->isd = 1972;
		$this->fighters = array("light"=>18);
	
        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 1.0;
        $this->accelcost = 3;
        $this->rollcost = 4;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(5, 17, 0, 0));
        $this->addPrimarySystem(new CnC(6, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 15, 4, 7));
        $this->addPrimarySystem(new Engine(5, 16, 0, 9, 4));
		$this->addPrimarySystem(new Hangar(5, 20));
   
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new LaserCutter(3, 6, 4, 240, 360));
        $this->addFrontSystem(new LaserCutter(3, 6, 4, 300, 60));
        $this->addFrontSystem(new LaserCutter(3, 6, 4, 300, 60));
        $this->addFrontSystem(new LaserCutter(3, 6, 4, 0, 120));
        $this->addFrontSystem(new LightLaser(2, 4, 3, 300, 60));
        $this->addFrontSystem(new LightLaser(2, 4, 3, 300, 60));

        $this->addAftSystem(new Thruster(4, 20, 0, 5, 2));
        $this->addAftSystem(new Thruster(4, 20, 0, 5, 2));
		$this->addAftSystem(new LaserCutter(3, 6, 4, 120, 240));
		$this->addAftSystem(new JumpEngine(4, 15, 5, 36));

		$this->addLeftSystem(new EWGatlingLaser(2, 7, 4, 240, 60));
		$this->addLeftSystem(new EWGatlingLaser(2, 7, 4, 120, 300));
        $this->addLeftSystem(new EWEMTorpedo(3, 6, 5, 240, 360));
        $this->addLeftSystem(new Thruster(3, 15, 0, 4, 3));

		$this->addRightSystem(new EWGatlingLaser(2, 7, 4, 300, 120));
		$this->addRightSystem(new EWGatlingLaser(2, 7, 4, 60, 240));
        $this->addRightSystem(new EWEMTorpedo(3, 6, 5, 0, 120));
        $this->addRightSystem(new Thruster(3, 15, 0, 4, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 48));
        $this->addAftSystem(new Structure(4, 45));
        $this->addLeftSystem(new Structure(4, 50));
        $this->addRightSystem(new Structure(4, 50));
        $this->addPrimarySystem(new Structure(5, 44));
		
		$this->hitChart = array(
			0=> array(
					9 => "Structure",
					11 => "Scanner",
					14 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					9 => "Laser Cutter",
					10 => "Light Laser",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					9 => "Jump Engine",
					10 => "Laser Cutter",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					5 => "Thruster",
					7 => "EM Torpedo",
					10 => "Gatling Laser",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					7 => "EM Torpedo",
					10 => "Gatling Laser",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
