<?php
class Seffensa extends BaseShip{
    
    function __construct($id, $userid, $name,  $movement){
        parent::__construct($id, $userid, $name,  $movement);
        
        $this->pointCost = 625;
        $this->faction = "Balosian";
        $this->phpclass = "Seffensa";
        $this->imagePath = "img/ships/seffensa.png";
        $this->shipClass = "Seffensa";
        $this->shipSizeClass = 3;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.50;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 10;
         
        $this->addPrimarySystem(new Reactor(6, 22, 0, 0));
        $this->addPrimarySystem(new CnC(6, 18, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 12, 4, 7));
        $this->addPrimarySystem(new Engine(5, 20, 0, 12, 3));
        $this->addPrimarySystem(new Hangar(6, 2));

        $this->addFrontSystem(new StdParticleBeam(3, 4, 1, 180, 60));
        $this->addFrontSystem(new StdParticleBeam(3, 4, 1, 180, 60));
        $this->addFrontSystem(new StdParticleBeam(3, 4, 1, 300, 180));
        $this->addFrontSystem(new StdParticleBeam(3, 4, 1, 300, 180));
        $this->addFrontSystem(new Assaultlaser(4, 6, 4, 300, 60));
        $this->addFrontSystem(new Thruster(5, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(5, 10, 0, 3, 1));

        $this->addAftSystem(new Thruster(4, 14, 0, 6, 2));
        $this->addAftSystem(new Thruster(4, 14, 0, 6, 2));

        $this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));
        $this->addLeftSystem(new AssaultLaser(4, 6, 4, 240, 0));
        $this->addLeftSystem(new IonCannon(4, 6, 4, 240, 0));

	$this->addRightSystem(new Thruster(4, 15, 0, 5, 4));
        $this->addRightSystem(new AssaultLaser(4, 6, 4, 0, 120));
        $this->addRightSystem(new IonCannon(4, 6, 4, 0, 120));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 44));
        $this->addAftSystem(new Structure( 4, 44));
        $this->addLeftSystem(new Structure( 4, 56));
        $this->addRightSystem(new Structure( 4, 56));
        $this->addPrimarySystem(new Structure( 6, 44));
    }
}
?>
