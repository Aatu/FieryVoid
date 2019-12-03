<?php
class Coqari extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 750;
		$this->faction = "Cascor";
        $this->phpclass = "Coqari";
        $this->imagePath = "img/ships/CascorCrocti.png";
        $this->shipClass = "Coqari Scout";
        $this->shipSizeClass = 3;
	    $this->limited = 33;
        $this->isd = 2226;
	    
        $this->fighters = array("medium"=>12);
        
        $this->forwardDefense = 16;
        $this->sideDefense = 17;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 6;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
         
        $this->addPrimarySystem(new Reactor(4, 20, 0, 0));
        $this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new ElintScanner(4, 24, 6, 10));
        $this->addPrimarySystem(new Engine(4, 20, 0, 12, 8));
		$this->addPrimarySystem(new Hangar(4, 14));
		$this->addPrimarySystem(new JumpEngine(4, 16, 4, 24));
        
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new IonFieldGenerator(3, 8, 4, 300, 60));
        $this->addFrontSystem(new DualIonBolter(2, 4, 4, 270, 90));
        $this->addFrontSystem(new IonFieldGenerator(3, 8, 4, 300, 60));
        
        $this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
	$this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
        $this->addAftSystem(new DualIonBolter(2, 4, 4, 120, 300));
        $this->addAftSystem(new DualIonBolter(2, 4, 4, 60, 240));
                
		$this->addLeftSystem(new Thruster(4, 15, 0, 6, 3));
		$this->addLeftSystem(new DualIonBolter(2, 4, 4, 240, 60));
		$this->addLeftSystem(new IonFieldGenerator(3, 8, 4, 300, 60));
		
		$this->addRightSystem(new Thruster(4, 15, 0, 6, 4));
		$this->addRightSystem(new DualIonBolter(2, 4, 4, 300, 120));
		$this->addRightSystem(new IonFieldGenerator(3, 8, 4, 300, 60));
		
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 50));
        $this->addAftSystem(new Structure( 4, 50));
        $this->addLeftSystem(new Structure( 4, 60));
        $this->addRightSystem(new Structure( 4, 60));
        $this->addPrimarySystem(new Structure( 4, 60));

        $this->hitChart = array(
                0=> array(
                        9 => "Structure",
                        11 => "Jump Engine",
                        13 => "ELINT Scanner",
                        15 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        5 => "Thruster",
                        7 => "Ion Field Generator",
                        8 => "Dual Ion Bolter",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        8 => "Dual Ion Bolter",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        5 => "Thruster",
                        7 => "Ion Field Generator",
                        8 => "Dual Ion Bolter",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        5 => "Thruster",
                        7 => "Ion Field Generator",
                        8 => "Dual Ion Bolter",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );

    }

}
?>
