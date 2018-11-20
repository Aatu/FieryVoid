<?php
class Capital extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 600;
        $this->faction = "UC";
        $this->phpclass = "Capital";
        $this->shipClass = "Vault Industries Mk I Capital";
        $this->shipModel = "Capital";
	    $this->isd = 2241;
        
        
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 1;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;
        
         
        $this->addPrimarySystem(new Reactor(5, 20, 0, 0));
        $this->addPrimarySystem(new CnC(6, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 4, 7));
        $this->addPrimarySystem(new Engine(5, 15, 0, 8, 2));
        $this->addPrimarySystem(new Hangar(5, 2));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 5, [4, 5]));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 5, [1, 2]));        
        
        
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 0));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 0));
        $this->addFrontSystem(new MediumPulse(3, 6, 3, 240, 0));
        $this->addFrontSystem(new MediumPulse(3, 6, 3, 240, 0));
        $this->addFrontSystem(new MediumPulse(3, 6, 3, 0, 120));
        $this->addFrontSystem(new MediumPulse(3, 6, 3, 0, 120));
        $this->addFrontSystem(new InterceptorMkI(2, 4, 1, 270, 90));
        $this->addFrontSystem(new RailGun(4, 9, 6, 0, 0));
		
        $this->addAftSystem(new RailGun(4, 9, 6, 0, 0));
        $this->addAftSystem(new SMissileRack(3, 6, 0, 240, 0));
        $this->addAftSystem(new SMissileRack(3, 6, 0, 0, 120));        
        $this->addAftSystem(new InterceptorMkI(2, 4, 1, 90, 270));
        $this->addAftSystem(new Thruster(4, 7, 0, 2, 3));
        $this->addAftSystem(new Thruster(4, 7, 0, 2, 3));
        $this->addAftSystem(new Thruster(4, 7, 0, 2, 3));
        $this->addAftSystem(new Thruster(4, 7, 0, 2, 3));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 48));
        $this->addAftSystem(new Structure( 4, 42));
        $this->addPrimarySystem(new Structure( 5, 50));
        
        
        $this->hitChart = array(
                0=> array(
                        8 => "Structure",
                        11 => "Thruster",
                        13 => "Scanner",
                        15 => "Engine",
                        16 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        3 => "Thruster",
                        5 => "Medium Pulse Cannon",
                        7 => "Railgun",
                        9 => "Interceptor MK I",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        8 => "Class-S Missile Rack",
                        10 => "Railgun",
                        12 => "Interceptor MK I",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );
    }

}



?>
