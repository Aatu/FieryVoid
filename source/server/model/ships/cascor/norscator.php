<?php
class Norscator extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 825;
        $this->faction = "Cascor";
        $this->phpclass = "Norscator";
        $this->imagePath = "img/ships/CascorNorsca.png";
        $this->shipClass = "Norscator Gunship";
        $this->shipSizeClass = 3;
        $this->isd = 2231;
        $this->occurence = "rare";
        $this->variantOf = "Norsca Battlecruiser";
        
        $this->fighters = array("heavy"=>12);
        
        $this->forwardDefense = 17;
        $this->sideDefense = 17;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.5;
        $this->accelcost = 6;
        $this->rollcost = 4;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(4, 23, 0, 0));
        $this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 4, 7));
        $this->addPrimarySystem(new Engine(4, 20, 0, 12, 6));
        $this->addPrimarySystem(new Hangar(4, 14));
        $this->addPrimarySystem(new IonicLaser(4, 6, 4, 300, 60));
        $this->addPrimarySystem(new IonicLaser(4, 6, 4, 300, 60));
        
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new IonicLaser(4, 6, 4, 300, 60));
        $this->addFrontSystem(new IonicLaser(4, 6, 4, 300, 60));
        
        $this->addAftSystem(new Thruster(4, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 3, 2));
        $this->addAftSystem(new JumpEngine(4, 16, 4, 24));
        $this->addAftSystem(new DualIonBolter(2, 4, 4, 120, 300));
        $this->addAftSystem(new IonicLaser(3, 6, 4, 120, 240));
        $this->addAftSystem(new IonicLaser(3, 6, 4, 120, 240));
        $this->addAftSystem(new DualIonBolter(2, 4, 4, 60, 240));
        
        $this->addLeftSystem(new Thruster(4, 15, 0, 6, 3));
        $this->addLeftSystem(new IonicLaser(3, 6, 4, 240, 360));
        $this->addLeftSystem(new IonicLaser(3, 6, 4, 240, 360));
        $this->addLeftSystem(new DualIonBolter(2, 4, 4, 240, 60));
        
        $this->addRightSystem(new Thruster(4, 15, 0, 6, 4));
        $this->addRightSystem(new IonicLaser(3, 6, 4, 0, 120));
        $this->addRightSystem(new IonicLaser(3, 6, 4, 0, 120));
        $this->addRightSystem(new DualIonBolter(2, 4, 4, 300, 120));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 40));
        $this->addAftSystem(new Structure( 4, 40));
        $this->addLeftSystem(new Structure( 4, 48));
        $this->addRightSystem(new Structure( 4, 48));
        $this->addPrimarySystem(new Structure( 4, 60));
        
        $this->hitChart = array(
            0=> array(
                8 => "Structure",
                11 => "Ionic Laser",
                13 => "Scanner",
                15 => "Engine",
                17 => "Hangar",
                19 => "Reactor",
                20 => "C&C",
            ),
            1=> array(
                5 => "Thruster",
                7 => "Ionic Laser",
                18 => "Structure",
                20 => "Primary",
            ),
            2=> array(
                6 => "Thruster",
                8 => "Jump Engine",
                10 => "Ionic Laser",
                12 => "Dual Ion Bolter",
                18 => "Structure",
                20 => "Primary",
            ),
            3=> array(
                3 => "Thruster",
                7 => "Ionic Laser",
                9 => "Dual Ion Bolter",
                18 => "Structure",
                20 => "Primary",
            ),
            4=> array(
                3 => "Thruster",
                7 => "Ionic Laser",
                9 => "Dual Ion Bolter",
                18 => "Structure",
                20 => "Primary",
            ),
        );
        
    }
    
}
