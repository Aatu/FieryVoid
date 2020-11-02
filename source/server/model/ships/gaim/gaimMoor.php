<?php
class GaimMoor extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 570;
        $this->faction = "Custom Ships"; //temporary - until Gaim fleet is ready :)
        $this->phpclass = "GaimMoor";
        $this->imagePath = "img/ships/altarian.png";
        $this->shipClass = "Moor Torpedo Destroyer";
        //$this->variantOf = "Suom Light Carrier";
        //$this->occurence = "uncommon";
        $this->isd = 2256;
        //$this->fighters = array("medium"=>6);
        
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.50;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 30;
        
         
        $this->addPrimarySystem(new Reactor(6, 17, 0, 0));
        $this->addPrimarySystem(new CnC(6, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 5, 8));
        $this->addPrimarySystem(new Engine(5, 15, 0, 10, 3));
        $this->addPrimarySystem(new Hangar(5, 1));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 4));
        
        
        
        $this->addFrontSystem(new SubReactorUniversal(4, 8));
        $this->addFrontSystem(new Bulkhead(0, 3));		
        $this->addFrontSystem(new PacketTorpedo(3, 0, 0, 240, 0));
        $this->addFrontSystem(new PacketTorpedo(3, 0, 0, 240, 0));
        $this->addFrontSystem(new PacketTorpedo(3, 0, 0, 0, 120));
        $this->addFrontSystem(new PacketTorpedo(3, 0, 0, 0, 120));
        $this->addFrontSystem(new PacketTorpedo(3, 0, 0, 300, 60));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
		
        
		
        $this->addAftSystem(new JumpEngine(3, 10, 3, 20));
        $this->addAftSystem(new Bulkhead(0, 3));
        $this->addAftSystem(new Bulkhead(0, 3));
        $this->addAftSystem(new ScatterGun(3, 0, 0, 120, 0));
        $this->addAftSystem(new ScatterGun(3, 0, 0, 0, 240));
        $this->addAftSystem(new Thruster(4, 14, 0, 5, 2));
        $this->addAftSystem(new Thruster(4, 14, 0, 5, 2));
        

        
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 60));
        $this->addAftSystem(new Structure( 4, 60));
        $this->addPrimarySystem(new Structure( 6, 46 ));
        
            $this->hitChart = array(
                0=> array(
                    6 => "Structure",
                    10 => "Thruster",
                    13 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
                ),
                1=> array(
                    3 => "Thruster",
                    7 => "Packet Torpedo",
                    9 => "Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
                ),
                2=> array(
                    5 => "Thruster",
                    7 => "Scattergun",
                    9 => "Jump Engine",
                    18 => "Structure",
                    20 => "Primary",
			),
		);   
        
        
    }

}



?>
