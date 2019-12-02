<?php
class Qoricc extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 500;
		$this->faction = "Cascor";
        $this->phpclass = "Qoricc";
        $this->imagePath = "img/ships/CascorQorric.png";
        $this->shipClass = "Qoricc Destroyer";
        $this->shipSizeClass = 3;
        $this->isd = 2220;
	    
        $this->fighters = array("medium"=>12, "ultralight"=>12);
        
        $this->forwardDefense = 16;
        $this->sideDefense = 16;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.33;
        $this->accelcost = 5;
        $this->rollcost = 4;
        $this->pivotcost = 4;
        $this->iniativebonus = 0;
         
        $this->addPrimarySystem(new Reactor(4, 20, 0, 0));
        $this->addPrimarySystem(new CnC(4, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 4, 6));
        $this->addPrimarySystem(new Engine(4, 21, 0, 9, 5));
		$this->addPrimarySystem(new Hangar(4, 14));
        
        $this->addFrontSystem(new Thruster(4, 15, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 15, 0, 3, 1));
        $this->addFrontSystem(new DualIonBolter(2, 4, 4, 240, 60));
        $this->addFrontSystem(new IonicLaser(3, 6, 4, 300, 60));
        $this->addFrontSystem(new DualIonBolter(2, 4, 4, 300, 120));
        
        $this->addAftSystem(new Thruster(4, 13, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 13, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 13, 0, 3, 2));
        $this->addAftSystem(new Hangar(4, 6));
        $this->addAftSystem(new DualIonBolter(2, 4, 4, 120, 300));
        $this->addAftSystem(new DualIonBolter(2, 4, 4, 60, 240));
                
		$this->addLeftSystem(new Thruster(4, 20, 0, 5, 3));
        $this->addLeftSystem(new IonicLaser(3, 6, 4, 240, 360));
        $this->addLeftSystem(new IonicLaser(3, 6, 4, 240, 360));
        
		$this->addRightSystem(new Thruster(4, 20, 0, 5, 4));
		$this->addRightSystem(new IonicLaser(3, 6, 4, 0, 120));
		$this->addRightSystem(new IonicLaser(3, 6, 4, 0, 120));
		
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 28));
        $this->addAftSystem(new Structure( 4, 28));
        $this->addLeftSystem(new Structure( 4, 40));
        $this->addRightSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 4, 48));

        $this->hitChart = array(
                0=> array(
                        8 => "Structure",
                        10 => "Scanner",
                        13 => "Engine",
                        16 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        7 => "Thruster",
                        9 => "Ionic Laser",
                        11 => "Dual Ion Bolter",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        7 => "Thruster",
                        9 => "Hangar",
                        11 => "Dual Ion Bolter",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
                        7 => "Ionic Laser",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
                        7 => "Ionic Laser",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );

    }

}
