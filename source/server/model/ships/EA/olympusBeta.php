<?php
class OlympusBeta extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 625;
        $this->faction = "EA";
        $this->phpclass = "OlympusBeta";
        $this->imagePath = "img/ships/olympus.png";
        $this->shipClass = "Olympus Gunship (Beta)";
        $this->occurence = "rare";
        $this->variantOf = "Olympus Corvette (Delta)";
	    $this->isd = 2229;
        $this->notes = 'Uncommon during Dilgar War.';
        
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 1;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;
        
        $this->addPrimarySystem(new Reactor(5, 20, 0, -4));
        $this->addPrimarySystem(new CnC(6, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 14, 4, 6));
        $this->addPrimarySystem(new Engine(5, 15, 0, 8, 2));
        $this->addPrimarySystem(new Hangar(5, 2));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 5, 4));
        
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new MediumLaser(3, 6, 5, 240, 0));
        $this->addFrontSystem(new MediumLaser(3, 6, 5, 240, 0));
        $this->addFrontSystem(new MediumLaser(3, 6, 5, 0, 120));
        $this->addFrontSystem(new MediumLaser(3, 6, 5, 0, 120));
        $this->addFrontSystem(new InterceptorMkI(2, 4, 1, 270, 90));
        $this->addFrontSystem(new ParticleCannon(4, 8, 7, 0, 0));
        
        $this->addAftSystem(new ParticleCannon(3, 8, 7, 240, 0));
        $this->addAftSystem(new ParticleCannon(4, 8, 7, 0, 0));
        $this->addAftSystem(new ParticleCannon(3, 8, 7, 0, 120));
        $this->addAftSystem(new InterceptorMkI(2, 4, 1, 90, 270));
        $this->addAftSystem(new Thruster(4, 7, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 7, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 7, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 7, 0, 2, 2));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 48));
        $this->addAftSystem(new Structure( 5, 42));
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
                        5 => "Medium Laser",
                        7 => "Particle Cannon",
                        9 => "Interceptor I",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        10 => "Particle Cannon",
                        12 => "Interceptor I",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );
        
    }
}


?>
