<?php
class gaimBassari extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 650;
		$this->faction = 'Gaim';
		$this->phpclass = "gaimBassari";
		$this->imagePath = "img/ships/GaimBassari.png";
		$this->shipClass = "Bassari Heavy Cruiser";
		$this->shipSizeClass = 3;
		$this->fighters = array("normal"=>12);
			$this->limited = 10;
	    
        $this->isd = 2258;

		$this->forwardDefense = 15;
		$this->sideDefense = 17;

		$this->turncost = 0.66;
		$this->turndelaycost = 0.66;
		$this->accelcost = 2;
		$this->rollcost = 3;
		$this->pivotcost = 2;

		$this->iniativebonus = 5;

		$this->addPrimarySystem(new Reactor(4, 16, 0, 2));
		$this->addPrimarySystem(new CnC(4, 12, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 14, 4, 7));
		$this->addPrimarySystem(new Engine(4, 16, 0, 8, 3));
		$this->addPrimarySystem(new Hangar(5, 14));

		$this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
		$this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
		$this->addFrontSystem(new PacketTorpedo(3, 6, 5, 300, 60));
		$this->addFrontSystem(new TwinArray(2, 6, 2, 240, 60));
		$this->addFrontSystem(new TwinArray(2, 6, 2, 300, 120));
		$this->addFrontSystem(new ScatterGun(3, 8, 3, 240, 60));
		$this->addFrontSystem(new ScatterGun(3, 8, 3, 300, 120));
		$this->addFrontSystem(new Bulkhead(0, 2));
		$this->addFrontSystem(new Bulkhead(0, 2));

		$this->addAftSystem(new Thruster(3, 10, 0, 4, 2));
		$this->addAftSystem(new Thruster(3, 10, 0, 4, 2));
		$this->addAftSystem(new TwinArray(2, 6, 2, 180, 300));
		$this->addAftSystem(new TwinArray(2, 6, 2, 60, 180));
		$this->addAftSystem(new PacketTorpedo(3, 6, 5, 120, 240));
		$this->addAftSystem(new ScatterGun(3, 8, 3, 120, 240));
		$this->addAftSystem(new ScatterGun(3, 8, 3, 120, 240));
		$this->addAftSystem(new Bulkhead(0, 2));
		$this->addAftSystem(new Bulkhead(0, 2));

		$this->addLeftSystem(new Thruster(3, 15, 0, 5, 3));
		$this->addLeftSystem(new TwinArray(2, 6, 2, 180, 360));
		$this->addLeftSystem(new TwinArray(2, 6, 2, 180, 360));
		$this->addLeftSystem(new AssaultLaser(3, 6, 4, 300, 360));
		$this->addLeftSystem(new Bulkhead(0, 2));
		$this->addLeftSystem(new Bulkhead(0, 2));

		$this->addRightSystem(new Thruster(3, 15, 0, 5, 4));
		$this->addRightSystem(new TwinArray(2, 6, 2, 0, 180));
		$this->addRightSystem(new TwinArray(2, 6, 2, 0, 180));
		$this->addRightSystem(new AssaultLaser(3, 6, 4, 0, 60));
		$this->addRightSystem(new Bulkhead(0, 2));
		$this->addRightSystem(new Bulkhead(0, 2));
        
        $this->addFrontSystem(new Structure( 4, 32));
        $this->addAftSystem(new Structure( 4, 32));
        $this->addLeftSystem(new Structure( 4, 40));
        $this->addRightSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 4, 36));
		
		
		$this->hitChart = array(
                0=> array(
                        8 => "Structure",
                        11 => "Scanner",
                        14 => "Engine",
                        16 => "Hangar",
                        18 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
						6 => "Twin Array",
						9 => "Scattergun",
                        10 => "Packet Torpedo",
                        17 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        4 => "Thruster",
						6 => "Twin Array",
						9 => "Scattergun",
                        10 => "Packet Torpedo",
                        17 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        3 => "Thruster",
						5 => "Assault Laser",
                        8 => "Twin Array",
                        17 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        3 => "Thruster",
						5 => "Assault Laser",
                        8 => "Twin Array",
                        17 => "Structure",
                        20 => "Primary",
                ),
        );

    }
}
?>
