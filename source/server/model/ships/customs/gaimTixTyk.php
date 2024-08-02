<?php
class gaimTixTyk extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 575;
		$this->faction = 'Custom Ships';
        $this->phpclass = "gaimTixTyk";
        $this->imagePath = "img/ships/Tixtyk.png";
        $this->shipClass = "Tixtyk Heavy Destroyer";
		$this->fighters = array("normal"=>6);        
        
		$this->isd = 2266;
		
        $this->forwardDefense = 14;
        $this->sideDefense = 12;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.666;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;

        $this->addPrimarySystem(new Reactor(5, 17, 0, 3));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 5, 7));
        $this->addPrimarySystem(new Engine(5, 15, 0, 10, 3));
        $this->addPrimarySystem(new Hangar(5, 8));
        $this->addPrimarySystem(new GaimPhotonBomb(4, 0, 0, 180, 360));
        $this->addPrimarySystem(new GaimPhotonBomb(4, 0, 0, 0, 180)); 
               
        $this->addFrontSystem(new Thruster(4, 9, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 9, 0, 3, 1));               
        $this->addAftSystem(new Thruster(4, 12, 0, 5, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 5, 2));        

        $this->addLeftSystem(new PacketTorpedo(4, 6, 5, 300, 60));
        $this->addLeftSystem(new TwinArray(3, 6, 2, 240, 60));
        $this->addLeftSystem(new BattleLaser(4, 6, 6, 240, 360));
        $this->addLeftSystem(new Thruster(4, 15, 0, 4, 3));
		$this->addLeftSystem(new Bulkhead(0, 4));

        $this->addRightSystem(new PacketTorpedo(4, 6, 5, 300, 60));
        $this->addRightSystem(new TwinArray(3, 6, 2, 300, 120));
        $this->addRightSystem(new BattleLaser(4, 6, 6, 0, 120));
        $this->addRightSystem(new Thruster(4, 15, 0, 4, 4));
		$this->addRightSystem(new Bulkhead(0, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(5, 32));
        $this->addLeftSystem(new Structure(4, 40));
        $this->addRightSystem(new Structure(4, 40));
		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					10 => "1:Thruster",
					12 => "2:Thruster",
					14 => "Photon Bomb",
					15 => "Scanner",
					16 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			3=> array(
					3 => "Thruster",
					5 => "Battle Laser",
					6 => "Twin Array",
					8 => "Packet Torpedo",				
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					3 => "Thruster",
					5 => "Battle Laser",
					6 => "Twin Array",
					8 => "Packet Torpedo",				
					18 => "Structure",
					20 => "Primary",
			),
		);
		
    }
}
