<?php
class Qoccata extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 950;
		$this->faction = "Cascor";
        $this->phpclass = "Qoccata";
        $this->imagePath = "img/ships/CascorQoccata.png";
        $this->shipClass = "Qoccata Supercarrier";
        $this->shipSizeClass = 3;
        $this->isd = 2231;
        $this->limited = 33;
	    
        $this->fighters = array("heavy"=>24, "medium"=>24, "ultralight"=>24);
        
        $this->forwardDefense = 18;
        $this->sideDefense = 19;
        
        $this->turncost = 1;
        $this->turndelaycost = 0.66;
        $this->accelcost = 8;
        $this->rollcost = 6;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
         
        $this->addPrimarySystem(new Reactor(5, 25, 0, 0));
        $this->addPrimarySystem(new CnC(5, 24, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 20, 5, 8));
        $this->addPrimarySystem(new Engine(5, 30, 0, 16, 9));
		$this->addPrimarySystem(new Hangar(5, 26));
		
        $this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));
        $this->addFrontSystem(new DualIonBolter(2, 4, 4, 240, 60));
        $this->addFrontSystem(new RadCannon(4, 8, 6, 300, 60));
        $this->addFrontSystem(new IonTorpedo(3, 5, 4, 300, 60));
        $this->addFrontSystem(new RadCannon(4, 8, 6, 300, 60));
        $this->addFrontSystem(new DualIonBolter(2, 4, 4, 300, 120));
        
        $this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
        $this->addAftSystem(new JumpEngine(4, 16, 4, 24));
        $this->addAftSystem(new Hangar(4, 12));
        $this->addAftSystem(new Hangar(4, 12));
        $this->addAftSystem(new IonCannon(3, 6, 4, 180, 300));
        $this->addAftSystem(new IonCannon(3, 6, 4, 60, 180));
        $this->addAftSystem(new DualIonBolter(2, 4, 4, 120, 300));
        $this->addAftSystem(new DualIonBolter(2, 4, 4, 60, 240));
                
		$this->addLeftSystem(new Thruster(4, 20, 0, 8, 3));
		$this->addLeftSystem(new Hangar(4, 6));
		$this->addLeftSystem(new IonTorpedo(3, 5, 4, 240, 360));
        $this->addLeftSystem(new DualIonBolter(2, 4, 4, 180, 360));
        $this->addLeftSystem(new IonCannon(3, 6, 4, 240, 360));
        $this->addLeftSystem(new IonCannon(3, 6, 4, 240, 360));
        
		$this->addRightSystem(new Thruster(4, 20, 0, 8, 4));
		$this->addRightSystem(new IonCannon(3, 6, 4, 0, 120));
		$this->addRightSystem(new IonCannon(3, 6, 4, 0, 120));
		$this->addRightSystem(new DualIonBolter(2, 4, 4, 0, 180));
		$this->addRightSystem(new IonTorpedo(3, 5, 4, 0, 120));
		$this->addRightSystem(new Hangar(4, 6));
		
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 50));
        $this->addAftSystem(new Structure( 4, 50));
        $this->addLeftSystem(new Structure( 4, 60));
        $this->addRightSystem(new Structure( 4, 60));
        $this->addPrimarySystem(new Structure( 5, 72));

        $this->hitChart = array(
                0=> array(
                        10 => "Structure",
                        12 => "Scanner",
                        14 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        5 => "Thruster",
                        7 => "Rad Cannon",
                        9 => "Dual Ion Bolter",
                        10 => "Ion Torpedo",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        8 => "Jump Engine",
                        10 => "Hangar",
                        12 => "Ion Cannon",
                        14 => "Dual Ion Bolter",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        3 => "Thruster",
                        6 => "Ion Cannon",
                        8 => "Ion Torpedo",
                        10 => "Dual Ion Bolter",
                        11 => "Hangar",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        3 => "Thruster",
                        6 => "Ion Cannon",
                        8 => "Ion Torpedo",
                        10 => "Dual Ion Bolter",
                        11 => "Hangar",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );

    }

}
