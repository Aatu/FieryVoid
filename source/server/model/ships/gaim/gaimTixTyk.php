<?php
class gaimTixTyk extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 615;
	    $this->faction = "Gaim Intelligence";
        $this->phpclass = "gaimTixTyk";
        $this->imagePath = "img/ships/Tixtyk.png";
        $this->shipClass = "Tixtyk Line Destroyer";
		$this->fighters = array("light"=>6);        
        
		$this->isd = 2266;
		
        $this->forwardDefense = 15;
        $this->sideDefense = 12;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 6 * 5;
		$this->unofficial = true;

        $this->addPrimarySystem(new Reactor(5, 20, 0, 2));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 6, 7));
        $this->addPrimarySystem(new Engine(5, 15, 0, 10, 3));
        $this->addPrimarySystem(new Hangar(4, 3));
        $this->addPrimarySystem(new Hangar(4, 6));
        $this->addPrimarySystem(new Scattergun(4, 8, 3, 0, 360));        
		
        //$this->addFrontSystem(new PacketTorpedo(4, 0, 0, 300, 60));		
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));   

        $this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 4, 2));        
        $this->addAftSystem(new TwinArray(3, 6, 2, 90, 270));
		$this->addAftSystem(new JumpEngine(3, 10, 3, 20));        

        $this->addLeftSystem(new PacketTorpedo(4, 0, 0, 240, 60));         
        $this->addLeftSystem(new BattleLaser(4, 6, 6, 240, 360));       		
        //$this->addLeftSystem(new GaimPhotonBomb(4, 0, 0, 180, 360));  
        $this->addLeftSystem(new TwinArray(3, 6, 2, 240, 60));
        $this->addLeftSystem(new Thruster(4, 15, 0, 4, 3));
		$this->addLeftSystem(new Bulkhead(0, 4));

//        $this->addRightSystem(new PacketTorpedo(4, 6, 5, 300, 60));
        $this->addRightSystem(new AssaultLaser(4, 6, 4, 300, 120));
        $this->addRightSystem(new AssaultLaser(4, 6, 4, 60, 240));		
        $this->addRightSystem(new PacketTorpedo(4, 0, 0, 300, 120));
        //$this->addRightSystem(new GaimPhotonBomb(4, 0, 0, 0, 180)); 
        $this->addRightSystem(new TwinArray(3, 6, 2, 300, 120));
        $this->addRightSystem(new Thruster(4, 15, 0, 4, 4));
		$this->addRightSystem(new Bulkhead(0, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(5, 32));
        $this->addLeftSystem(new Structure(5, 52));
        $this->addRightSystem(new Structure(5, 52));
		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
                    9 => "Scattergun",                    
					11 => "1:Thruster",
					13 => "2:Thruster",
					16 => "Hangar",                    
					17 => "Scanner",
					18 => "Engine",
					19 => "Reactor",
					20 => "C&C",
			),
			3=> array(
					4 => "Thruster",
					6 => "Battle Laser",
					8 => "Packet Torpedo",
					10 => "Twin Array",				
					19 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					6 => "Assault Laser",
					8 => "Packet Torpedo",
					10 => "Twin Array",				
					19 => "Structure",
					20 => "Primary",
			),
		);
		
    }
}
