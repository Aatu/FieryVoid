<?php
class Shafab extends BaseShip{
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        $this->pointCost = 625;
        $this->faction = "Markab";
        $this->phpclass = "Shafab";
        $this->isd = 2012;        
        $this->imagePath = "img/ships/MarkabCruiser.png"; 
        $this->shipClass = "Shafab Heavy Cruiser";
        $this->shipSizeClass = 3;
        $this->canvasSize = 200;
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 4;
        $this->fighters = array("normal"=>12);
        
        $this->addPrimarySystem(new Reactor(4, 17, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 18, 8, 8));
        $this->addPrimarySystem(new Engine(4, 12, 0, 6, 2));
        $this->addPrimarySystem(new Hangar(4, 16));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new ParticleCannon(4, 8, 7, 300, 60));
        $this->addFrontSystem(new ParticleCannon(4, 8, 7, 300, 60));
        $this->addFrontSystem(new PlasmaWaveTorpedo(4, 7, 4, 300, 60));
        
        $this->addAftSystem(new Thruster(4, 13, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 13, 0, 4, 2));
        $this->addAftSystem(new ScatterGun(2, 8, 3, 120, 360));
        $this->addAftSystem(new ScatterGun(2, 8, 3, 0, 240));
        $this->addLeftSystem(new Thruster(4, 13, 0, 3, 3));
        $this->addLeftSystem(new StunBeam(3, 6, 5, 300, 360));
        $this->addLeftSystem(new HeavyPlasma(4, 8, 5, 240, 360));
        $this->addLeftSystem(new ScatterGun(2, 8, 3, 180, 60));
        $this->addRightSystem(new Thruster(4, 13, 0, 3, 4));
        $this->addRightSystem(new StunBeam(3, 6, 5, 0, 60));
        $this->addRightSystem(new HeavyPlasma(4, 8, 5, 0, 120));
        $this->addRightSystem(new ScatterGun(2, 8, 3, 300, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 36));
        $this->addAftSystem(new Structure( 4, 36));
        $this->addLeftSystem(new Structure( 4, 49));
        $this->addRightSystem(new Structure( 4, 49));
        $this->addPrimarySystem(new Structure( 4, 36));
    
        $this->hitChart = array(
            0=> array(
                    9 => "Structure",
                    12 => "Scanner",
                    14 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    3 => "Thruster",
            		5 => "Plasma Wave",
            		8 => "Particle Cannon",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
            		9 => "Scattergun",
            		11 => "Jump Engine",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    4 => "Thruster",
                    6 => "Heavy Plasma Cannon",
                    10 => "Scattergun",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array(
                    4 => "Thruster",
                    6 => "Heavy Plasma Cannon",
                    10 => "Scattergun",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
