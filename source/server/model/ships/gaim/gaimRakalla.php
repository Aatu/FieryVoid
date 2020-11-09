<?php
class gaimRakalla extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 350;
		$this->faction = "Gaim";
        $this->phpclass = "gaimRakalla";
        $this->imagePath = "img/ships/GaimRakalla.png";
        $this->shipClass = "Rakalla Auxiliary Destroyer";
        $this->canvasSize = 100;

        $this->isd = 2245;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 999;
        $this->pivotcost = 999;
		$this->iniativebonus = 0;
		
        $this->addPrimarySystem(new Reactor(4, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 2, 4));
		$this->addPrimarySystem(new CnC(3, 5, 0, 0));
        $this->addPrimarySystem(new Engine(4, 6, 0, 4, 3));
        $this->addPrimarySystem(new Hangar(2, 4));
		$this->addPrimarySystem(new Thruster(3, 10, 0, 2, 3));
		$this->addPrimarySystem(new Thruster(3, 10, 0, 2, 4));
		$this->addPrimarySystem(new TwinArray(2, 6, 2, 0, 360));
		
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
		$this->addFrontSystem(new PacketTorpedo(3, 6, 5, 300, 0));
		$this->addFrontSystem(new PacketTorpedo(3, 6, 5, 0, 60));
		$this->addFrontSystem(new Bulkhead(0, 2));
		
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
		$this->addAftSystem(new TwinArray(2, 6, 2, 180, 360));
		$this->addAftSystem(new TwinArray(2, 6, 2, 0, 180));
		$this->addAftSystem(new Bulkhead(0, 2));
		$this->addAftSystem(new Bulkhead(0, 2));
       
        $this->addPrimarySystem(new Structure( 4, 52));
        
        $this->hitChart = array(
        		0=> array(
        				7 => "Thruster",
						9 => "Twin Array",
						12 => "Scanner",
						15 => "Engine",
        				17 => "Hangar",
						19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				5 => "Thruster",
        				9 => "Packet Torpedo",
        				17 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				5 => "Thruster",
						9 => "Twin Array",
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
?>
