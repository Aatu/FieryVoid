<?php
class gaimHadros extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 340;
		$this->faction = "Gaim";
        $this->phpclass = "gaimHadros";
        $this->imagePath = "img/ships/GaimHadros.png";
        $this->shipClass = "Hadros Patrol Frigate";
        $this->canvasSize = 100;

        $this->agile = true;
        $this->isd = 2246;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 12;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
		$this->iniativebonus = 70;
		
        $this->addPrimarySystem(new Reactor(4, 10, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 3, 6));
		$this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Hangar(4, 1));
        $this->addPrimarySystem(new Engine(3, 9, 0, 10, 2));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 4, 3));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 4, 4));
		
        $this->addFrontSystem(new Thruster(2, 8, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 8, 0, 2, 1));
        $this->addFrontSystem(new TwinArray(2, 6, 2, 180, 60));
        $this->addFrontSystem(new TwinArray(2, 6, 2, 300, 180));
		$this->addFrontSystem(new PacketTorpedo(3, 6, 5, 300, 60));
		$this->addFrontSystem(new Bulkhead(0, 2));
		
        $this->addAftSystem(new Thruster(2, 10, 0, 5, 2));
        $this->addAftSystem(new Thruster(2, 10, 0, 5, 2));
        $this->addAftSystem(new TwinArray(2, 6, 2, 120, 360));
        $this->addAftSystem(new TwinArray(2, 6, 2, 0, 240));
		$this->addAftSystem(new Bulkhead(0, 2));
       
        $this->addPrimarySystem(new Structure( 4, 33));
        
        $this->hitChart = array(
        		0=> array(
        				8 => "Thruster",
						11 => "Scanner",
						15 => "Engine",
						17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				6 => "Thruster",
        				8 => "Packet Torpedo",
        				12 => "Twin Array",
        				17 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				10 => "Twin Array",
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
?>
