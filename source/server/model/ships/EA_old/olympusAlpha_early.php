<?php
class OlympusAlpha_early extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 500;
        $this->faction = "EA (early)";
        $this->phpclass = "OlympusAlpha_early";
        $this->imagePath = "img/ships/olympus.png";
        $this->shipClass = "Olympus Corvette (Alpha)";
//        $this->variantOf = "Olympus Corvette (Delta)";
	    $this->isd = 2200;
                
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 1;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;
        
        $this->addPrimarySystem(new Reactor(4, 18, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 4, 5));
        $this->addPrimarySystem(new Engine(4, 11, 0, 6, 3));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 4, 4));
        
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 240, 0));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 240, 0));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 0, 120));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 0, 120));
        $this->addFrontSystem(new InterceptorMkI(2, 4, 1, 270, 90));
        $this->addFrontSystem(new RailGun(3, 9, 6, 0, 360));
        
        $this->addAftSystem(new SoMissileRack(3, 6, 0, 240, 0));
        $this->addAftSystem(new SoMissileRack(3, 6, 0, 0, 120));
        $this->addAftSystem(new InterceptorMkI(2, 4, 1, 0, 360));
        $this->addAftSystem(new Thruster(2, 6, 0, 1, 2));
        $this->addAftSystem(new Thruster(3, 7, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 7, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 6, 0, 1, 2));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 48));
        $this->addAftSystem(new Structure( 4, 42));
        $this->addPrimarySystem(new Structure( 4, 50));
        
        
    }

}



?>
