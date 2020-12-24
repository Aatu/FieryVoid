<?php
class gaimDraskar extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 450;
		$this->faction = "Gaim";
        $this->phpclass = "gaimDraskar";
        $this->imagePath = "img/ships/GaimDraskar.png";
        $this->shipClass = "Draskar Cruiser";
	    $this->fighters = array("normal"=>6);
        
		$this->notes = 'Atmospheric Capable';
		$this->isd = 2256;
		
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;

        $this->addPrimarySystem(new Reactor(7, 20, 0, 4));
        $this->addPrimarySystem(new CnC(8, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 12, 6, 8));
        $this->addPrimarySystem(new Engine(6, 14, 0, 10, 3));
        $this->addPrimarySystem(new Hangar(6, 8));
        $this->addPrimarySystem(new Thruster(6, 15, 0, 6, 1));
        $this->addPrimarySystem(new Thruster(6, 18, 0, 10, 2));
        $this->addPrimarySystem(new TwinArray(4, 6, 2, 90, 270));

        $this->addLeftSystem(new PacketTorpedo(5, 6, 5, 240, 360));
        $this->addLeftSystem(new TwinArray(4, 6, 2, 240, 60));
        $this->addLeftSystem(new TwinArray(2, 6, 2, 180, 360));
        $this->addLeftSystem(new Thruster(6, 15, 0, 6, 3));
		$this->addLeftSystem(new Bulkhead(0, 4));

        $this->addRightSystem(new PacketTorpedo(5, 6, 5, 0, 120));
        $this->addRightSystem(new TwinArray(4, 6, 2, 300, 120));
        $this->addRightSystem(new TwinArray(2, 6, 2, 0, 180));
        $this->addRightSystem(new Thruster(6, 15, 0, 6, 4));
		$this->addRightSystem(new Bulkhead(0, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(7, 40));
        $this->addLeftSystem(new Structure(6, 48));
        $this->addRightSystem(new Structure(6, 48));
		
		$this->hitChart = array(
			0=> array(
					9 => "Structure",
					11 => "Thruster",
					12 => "Twin Array",
					14 => "Scanner",
					16 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			3=> array(
					4 => "Thruster",
					8 => "Twin Array",
					10 => "Packet Torpedo",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					8 => "Twin Array",
					10 => "Packet Torpedo",
					18 => "Structure",
					20 => "Primary",
			),
		);
		
    }
}
