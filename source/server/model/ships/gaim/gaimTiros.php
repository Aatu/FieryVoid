<?php
class gaimTiros extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 650;
		$this->faction = 'Gaim';
		$this->phpclass = "gaimTiros";
		$this->imagePath = "img/ships/GaimTiac.png";
		$this->shipClass = "Tiros Cruiser";
			$this->variantOf = "Tiac Long Range Explorer";
			$this->occurence = "common";
		$this->shipSizeClass = 3;
		$this->fighters = array("normal"=>6);
	    
        $this->isd = 2252;

		$this->forwardDefense = 14;
		$this->sideDefense = 15;

		$this->turncost = 1;
		$this->turndelaycost = 1;
		$this->accelcost = 4;
		$this->rollcost = 4;
		$this->pivotcost = 4;

		$this->iniativebonus = 0;

		$this->addPrimarySystem(new Reactor(5, 20, 0, 0));
		$this->addPrimarySystem(new CnC(5, 12, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 16, 5, 8));
		$this->addPrimarySystem(new Engine(5, 20, 0, 10, 4));
		$this->addPrimarySystem(new Hangar(5, 8));

		$this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
		$this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
		$this->addFrontSystem(new HeavyPulse(4, 6, 4, 240, 360));
		$this->addFrontSystem(new HeavyPulse(4, 6, 4, 0, 120));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 180, 60));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 300, 180));
		$this->addFrontSystem(new Bulkhead(0, 4));
		$this->addFrontSystem(new Bulkhead(0, 4));

		$this->addAftSystem(new Thruster(4, 13, 0, 5, 2));
		$this->addAftSystem(new Thruster(4, 13, 0, 5, 2));
		$this->addAftSystem(new TwinArray(3, 6, 2, 120, 360));
		$this->addAftSystem(new TwinArray(3, 6, 2, 0, 240));
		$this->addAftSystem(new MediumPulse(4, 6, 3, 90, 270));
		$this->addAftSystem(new Bulkhead(0, 4));
		$this->addAftSystem(new Bulkhead(0, 4));

		$this->addLeftSystem(new Thruster(4, 15, 0, 4, 3));
		$this->addLeftSystem(new PacketTorpedo(4, 6, 5, 240, 360));
		$this->addLeftSystem(new TwinArray(3, 6, 2, 180, 360));
		$this->addLeftSystem(new Bulkhead(0, 4));
		$this->addLeftSystem(new Bulkhead(0, 4));

		$this->addRightSystem(new Thruster(4, 15, 0, 4, 4));
		$this->addRightSystem(new PacketTorpedo(4, 6, 5, 0, 120));
		$this->addRightSystem(new TwinArray(3, 6, 2, 0, 180));
		$this->addRightSystem(new Bulkhead(0, 4));
		$this->addRightSystem(new Bulkhead(0, 4));
        
        $this->addFrontSystem(new Structure( 5, 44));
        $this->addAftSystem(new Structure( 5, 44));
        $this->addLeftSystem(new Structure( 5, 48));
        $this->addRightSystem(new Structure( 5, 48));
        $this->addPrimarySystem(new Structure( 5, 44));
		
		
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
                        5 => "Twin Array",
						9 => "Heavy Pulse Cannon",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        7 => "Thruster",
						8 => "Medium Pulse Cannon",
						10 => "Twin Array",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
						6 => "Packet Torpedo",
						7 => "Twin Array",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
						6 => "Packet Torpedo",
						7 => "Twin Array",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );

    }
}
?>
