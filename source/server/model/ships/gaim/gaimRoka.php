<?php
class gaimRoka extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 400;
		$this->faction = "Gaim";
        $this->phpclass = "gaimRoka";
        $this->imagePath = "img/ships/gaimRoka.png";
        $this->shipClass = "Roka Auxiliary Cruiser";
        $this->canvasSize = 100;
		
		$this->fighters = array("light"=>6);


        $this->isd = 2249;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 999;
        $this->pivotcost = 999;
		$this->iniativebonus = 0;
		
        $this->addPrimarySystem(new Reactor(4, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 4, 5));
		$this->addPrimarySystem(new CnC(4, 9, 0, 0));
        $this->addPrimarySystem(new Hangar(3, 2));
        $this->addPrimarySystem(new Engine(3, 12, 0, 6, 3));
		$this->addPrimarySystem(new Thruster(3, 10, 0, 3, 3));
		$this->addPrimarySystem(new Thruster(3, 10, 0, 3, 4));
		$this->addPrimarySystem(new TwinArray(2, 6, 2, 0, 360));
		
        $this->addFrontSystem(new Thruster(3, 8, 0, 6, 1));
        $this->addFrontSystem(new TwinArray(2, 6, 2, 180, 60));
        $this->addFrontSystem(new TwinArray(2, 6, 2, 300, 180));
        $this->addFrontSystem(new Hangar(0, 3));
        $this->addFrontSystem(new Hangar(0, 3));
		$this->addFrontSystem(new PacketTorpedo(3, 6, 5, 300, 60));
		$this->addFrontSystem(new PacketTorpedo(3, 6, 5, 300, 60));
		$this->addFrontSystem(new Bulkhead(0, 3));
		
        $this->addAftSystem(new Thruster(3, 12, 0, 6, 2));
        $this->addAftSystem(new TwinArray(2, 6, 2, 120, 360));
        $this->addAftSystem(new TwinArray(2, 6, 2, 0, 240));
		$this->addAftSystem(new CargoBay(2, 20));
		$this->addAftSystem(new CargoBay(2, 20));
		$this->addAftSystem(new Bulkhead(0, 3));
       
        $this->addPrimarySystem(new Structure( 4, 56));
        
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
        				4 => "Thruster",
        				7 => "Packet Torpedo",
        				9 => "Twin Array",
        				17 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				3 => "Thruster",
						7 => "Cargo Bay",
        				11 => "Twin Array",
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
?>
