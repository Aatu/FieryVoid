<?php
class Vorchan_WI extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 525;
	$this->faction = "What If";
        $this->phpclass = "Vorchan_WI";
        $this->imagePath = "img/ships/vorchan.png";
        $this->shipClass = "Centauri Vorchan Warship";
	    $this->isd = 2160;

		$this->fighters = array("medium"=>6);

        $this->forwardDefense = 12;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus = 40;

        $this->addPrimarySystem(new Reactor(7, 12, 0, 4));
        $this->addPrimarySystem(new CnC(6, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 20, 4, 8));
        $this->addPrimarySystem(new Engine(7, 11, 0, 10, 2));
		$this->addPrimarySystem(new Hangar(6, 8));
		$this->addFrontSystem(new PlasmaAccelerator(4, 10, 5, 300, 60));
		$this->addAftSystem(new JumpEngine(6, 16, 3, 16));

        $this->addLeftSystem(new HeavyArray(3, 8, 4, 240, 120));
		$this->addLeftSystem(new Thruster(5, 10, 0, 3, 1));
		$this->addLeftSystem(new Thruster(5, 12, 0, 5, 2));
		$this->addLeftSystem(new Thruster(5, 15, 0, 5, 3));

        $this->addRightSystem(new HeavyArray(3, 8, 4, 240, 120));
		$this->addRightSystem(new Thruster(5, 10, 0, 3, 1));
		$this->addRightSystem(new Thruster(5, 12, 0, 5, 2));
		$this->addRightSystem(new Thruster(5, 15, 0, 5, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure( 6, 28));
        $this->addLeftSystem(new Structure(5, 48));
        $this->addRightSystem(new Structure(5, 48));
    
            $this->hitChart = array(
        		0=> array(
                    7 => "Structure",
					9 => "2:Jump Engine",
					10 => "1:Plasma Accelerator",
                    12 => "Scanner",
                    15 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
        		),
        		3=> array(
        				6 => "Thruster",
        				9 => "Heavy Array",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				6 => "Thruster",
        				9 => "Heavy Array",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    
    }
}
?>
