<?php
class KastanNightblade extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 500;
	$this->faction = "ZEscalation Kastan Monarchy";
        $this->phpclass = "KastanNightblade";
        $this->imagePath = "img/ships/EscalationWars/KastanIronblade.png";
        $this->shipClass = "Nightblade Scout Cruiser";
			$this->variantOf = "Ironblade Heavy Cruiser";
			$this->occurence = "uncommon";
        $this->shipSizeClass = 3;
		$this->canvasSize = 175; //img has 200px per side
		$this->unofficial = true;

        $this->fighters = array("normal"=>12);

		$this->isd = 1896;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 16;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 10;
        
        $this->addPrimarySystem(new Reactor(5, 20, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new ELINTScanner(4, 15, 4, 6));
        $this->addPrimarySystem(new Engine(4, 18, 0, 10, 2));
		$this->addPrimarySystem(new Hangar(4, 14));
		$this->addPrimarySystem(new JumpEngine(4, 15, 4, 24));
		
        $this->addFrontSystem(new Thruster(3, 13, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 13, 0, 4, 1));
		$this->addFrontSystem(new EWLaserBolt(2, 4, 2, 240, 60));
		$this->addFrontSystem(new ELINTScanner(4, 12, 3, 4));
		$this->addFrontSystem(new EWLaserBolt(2, 4, 2, 300, 120));

        $this->addAftSystem(new Thruster(3, 15, 0, 5, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 5, 2));
        $this->addAftSystem(new EWLaserBolt(2, 4, 2, 90, 270));
        $this->addAftSystem(new EWRoyalLaser(2, 6, 4, 90, 270));
        $this->addAftSystem(new EWLaserBolt(2, 4, 2, 90, 270));

        $this->addLeftSystem(new EWLaserBolt(3, 4, 2, 240, 360));
        $this->addLeftSystem(new EWRoyalLaser(2, 6, 4, 180, 360));
        $this->addLeftSystem(new EWRoyalLaser(2, 6, 4, 180, 360));
        $this->addLeftSystem(new Thruster(3, 15, 0, 5, 3));
		
        $this->addRightSystem(new EWLaserBolt(3, 4, 2, 0, 120));
        $this->addRightSystem(new EWRoyalLaser(2, 6, 4, 0, 180));
        $this->addRightSystem(new EWRoyalLaser(2, 6, 4, 0, 180));
        $this->addRightSystem(new Thruster(3, 15, 0, 5, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 48));
        $this->addAftSystem(new Structure(3, 48));
        $this->addLeftSystem(new Structure(3, 44));
        $this->addRightSystem(new Structure(3, 44));
        $this->addPrimarySystem(new Structure(4, 50));
		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					11 => "Jump Engine",
					13 => "ELINT Scanner",
					15 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					7 => "ELINT Scanner",
					9 => "Laser Bolt",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Royal Laser",
					10 => "Laser Bolt",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					5 => "Thruster",
					7 => "Laser Bolt",
					10 => "Royal Laser",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					7 => "Laser Bolt",
					10 => "Royal Laser",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
