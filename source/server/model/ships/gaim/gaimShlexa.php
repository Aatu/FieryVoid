<?php
class gaimShlexa extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 500;
        $this->faction = "Gaim Intelligence"; 
        $this->phpclass = "gaimShlexa";
        $this->imagePath = "img/ships/GaimShlexa.png";
        $this->shipClass = "Shlexa Ballistic Destroyer";
        $this->isd = 2266;
		$this->unofficial = true;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 1.0;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 30;
        
         
        $this->addPrimarySystem(new Reactor(5, 20, 0, 1));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 6, 7));
        $this->addPrimarySystem(new Engine(5, 15, 0, 10, 2));
        $this->addPrimarySystem(new Hangar(4, 2, 2));
        $this->addPrimarySystem(new Thruster(4, 15, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 15, 0, 4, 4));
        
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new PacketTorpedo(5, 6, 5, 240, 120));
        $this->addFrontSystem(new PacketTorpedo(5, 6, 5, 330, 30));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 180, 60));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 300, 180));
        $this->addFrontSystem(new Bulkhead(0, 3));
		
        $this->addAftSystem(new Thruster(4, 10, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 3, 2));
        $this->addAftSystem(new PacketTorpedo(5, 6, 5, 240, 360));
        $this->addAftSystem(new PacketTorpedo(5, 6, 5, 0, 120));
        $this->addAftSystem(new TwinArray(3, 6, 2, 60, 300));
        $this->addAftSystem(new Scattergun(4, 8, 3, 180, 360));        
        $this->addAftSystem(new Scattergun(4, 8, 3, 0, 180));        
        $this->addAftSystem(new Bulkhead(0, 3));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 60));
        $this->addAftSystem(new Structure( 5, 40));
        $this->addPrimarySystem(new Structure( 5, 25));
        
            $this->hitChart = array(
                0=> array(
                    6 => "Structure",
                    9 => "Thruster",
                    12 => "Scanner",
                    15 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
                ),
                1=> array(
                    3 => "Thruster",
                    6 => "Packet Torpedo",
					9 => "Twin Array",
                    18 => "Structure",
                    20 => "Primary",
                ),
                2=> array(
                    4 => "Thruster",
                    7 => "Scattergun",
                    10 => "Twin Array",
                    18 => "Structure",
                    20 => "Primary",
			),
		);   
        
        
    }

}



?>
