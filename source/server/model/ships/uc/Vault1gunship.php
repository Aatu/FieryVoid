<?php
class Vault1Gunship extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 600;
        $this->faction = "UC";
        $this->phpclass = "Vault1Gunship";
        $this->imagePath = "img/ships/olympus.png";
        $this->shipClass = "Vault Industries Mk I Gunship";
        $this->shipModel = "Gunship";
	    $this->isd = 2241;
        
        
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;
        $this->evasionCost = 3;
        
         
        $this->addPrimarySystem(101, new Reactor(5, 20, 0, 0));
        $this->addPrimarySystem(102, new CnC(6, 16, 0, 0));
        $this->addPrimarySystem(103, new Scanner(5, 16, 4, 7));
        $this->addPrimarySystem(104, new Engine(5, 15, 0, 15, 2));
        $this->addPrimarySystem(105, new Hangar(5, 2));
        $this->addPrimarySystem(106, new Thruster(3, 13, 0, 5, [4, 5]));
        $this->addPrimarySystem(107, new Thruster(3, 13, 0, 5, [1, 2]));        
        
        
        $this->addFrontSystem(200, new Thruster(3, 8, 0, 3, 0));
        $this->addFrontSystem(201, new Thruster(3, 8, 0, 3, 0));
        $this->addFrontSystem(202, new ManouveringThruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(203, new MediumPulse(3, 6, 3, 240, 0));
        $this->addFrontSystem(204, new MediumPulse(3, 6, 3, 240, 0));
        $this->addFrontSystem(205, new MediumPulse(3, 6, 3, 0, 120));
        $this->addFrontSystem(206, new MediumPulse(3, 6, 3, 0, 120));
        $this->addFrontSystem(207, new InterceptorMkI(2, 4, 1, 270, 90));
        $this->addFrontSystem(208, new RailGun(4, 9, 6, 0, 0));
		
        $this->addAftSystem(300, new RailGun(4, 9, 6, 0, 0));
        $this->addAftSystem(301, new SMissileRack(3, 6, 0, 240, 0));
        $this->addAftSystem(302, new SMissileRack(3, 6, 0, 0, 120));        
        $this->addAftSystem(303, new InterceptorMkI(2, 4, 1, 90, 270));
        $this->addAftSystem(304, new Thruster(4, 7, 0, 2, 3));
        $this->addAftSystem(305, new Thruster(4, 7, 0, 2, 3));
        $this->addAftSystem(306, new Thruster(4, 7, 0, 2, 3));
        $this->addAftSystem(307, new Thruster(4, 7, 0, 2, 3));
        $this->addAftSystem(308, new ManouveringThruster(3, 8, 0, 3, 1));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(1, new Structure( 5, 48));
        $this->addAftSystem(2, new Structure( 4, 42));
        $this->addPrimarySystem(3, new Structure( 5, 50));
    }

}



?>
