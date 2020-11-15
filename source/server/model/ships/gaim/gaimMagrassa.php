<?php
class gaimMagrassa extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 525;
		$this->faction = 'Gaim';
		$this->phpclass = "gaimMagrassa";
		$this->imagePath = "img/ships/GaimMagrassa.png";
		$this->shipClass = "Magrassa Strike Carrier";
		$this->shipSizeClass = 3;
		$this->fighters = array("normal"=>24);
	    
        $this->isd = 2252;

		$this->forwardDefense = 16;
		$this->sideDefense = 17;

		$this->turncost = 1;
		$this->turndelaycost = 0.66;
		$this->accelcost = 4;
		$this->rollcost = 3;
		$this->pivotcost = 6;

		$this->iniativebonus = -5;

		$this->addPrimarySystem(new Reactor(4, 18, 0, 0));
		$this->addPrimarySystem(new CnC(4, 12, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 16, 6, 7));
		$this->addPrimarySystem(new Engine(4, 14, 0, 8, 3));
		$this->addPrimarySystem(new Hangar(4, 8));

		$this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
		$this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
		$this->addFrontSystem(new Hangar(3, 6));
		$this->addFrontSystem(new PacketTorpedo(3, 6, 5, 240, 360));
		$this->addFrontSystem(new PacketTorpedo(3, 6, 5, 0, 120));
		$this->addFrontSystem(new PacketTorpedo(3, 6, 5, 300, 60));
		$this->addFrontSystem(new Bulkhead(0, 3));

		$this->addAftSystem(new Thruster(3, 13, 0, 4, 2));
		$this->addAftSystem(new Thruster(3, 13, 0, 4, 2));
		$this->addAftSystem(new Hangar(3, 12));
		$this->addAftSystem(new TwinArray(2, 6, 2, 120, 360));
		$this->addAftSystem(new TwinArray(2, 6, 2, 0, 240));
		$this->addAftSystem(new Bulkhead(0, 3));
		$this->addAftSystem(new Bulkhead(0, 3));

		$this->addLeftSystem(new Thruster(3, 13, 0, 3, 3));
		$this->addLeftSystem(new ScatterGun(2, 8, 3, 180, 60));
		$this->addLeftSystem(new Bulkhead(0, 3));

		$this->addRightSystem(new Thruster(3, 13, 0, 3, 4));
		$this->addRightSystem(new ScatterGun(2, 8, 3, 300, 180));
		$this->addRightSystem(new Bulkhead(0, 3));
        
        $this->addFrontSystem(new Structure( 4, 48));
        $this->addAftSystem(new Structure( 4, 40));
        $this->addLeftSystem(new Structure( 4, 56));
        $this->addRightSystem(new Structure( 4, 56));
        $this->addPrimarySystem(new Structure( 4, 40));
		
		
		$this->hitChart = array(
                0=> array(
                        9 => "Structure",
                        12 => "Scanner",
                        15 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        3 => "Thruster",
						5 => "Hangar",
                        10 => "Packet Torpedo",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        8 => "Hangar",
						10 => "Twin Array",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        5 => "Thruster",
                        7 => "Scattergun",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        5 => "Thruster",
                        7 => "Scattergun",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );

    }
}
?>
