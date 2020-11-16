<?php
class gaimSulo extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 600;
		$this->faction = 'Gaim';
		$this->phpclass = "gaimSulo";
		$this->imagePath = "img/ships/gaimSulo.png";
		$this->shipClass = "Sulo Fleet Carrier";
		$this->shipSizeClass = 3;
		$this->fighters = array("medium"=>36);
			$this->limited = 33;
	    
        $this->isd = 2254;

		$this->forwardDefense = 16;
		$this->sideDefense = 16;

		$this->turncost = 1;
		$this->turndelaycost = 1;
		$this->accelcost = 4;
		$this->rollcost = 3;
		$this->pivotcost = 3;

		$this->iniativebonus = 0;

		$this->addPrimarySystem(new Reactor(7, 20, 0, 1));
		$this->addPrimarySystem(new CnC(7, 20, 0, 0));
		$this->addPrimarySystem(new Scanner(7, 16, 5, 8));
		$this->addPrimarySystem(new Engine(6, 20, 0, 10, 3));
		$this->addPrimarySystem(new Hangar(7, 14));
		$this->addPrimarySystem(new JumpEngine(7, 18, 3, 16));

		$this->addFrontSystem(new Thruster(6, 8, 0, 2, 1));
		$this->addFrontSystem(new ScatterGun(3, 8, 3, 180, 60));
		$this->addFrontSystem(new ScatterGun(3, 8, 3, 300, 180));
		$this->addFrontSystem(new Bulkhead(0, 3));
		$this->addFrontSystem(new Bulkhead(0, 3));
		$this->addFrontSystem(new Bulkhead(0, 4));

		$this->addAftSystem(new Thruster(5, 15, 0, 5, 2));
		$this->addAftSystem(new Thruster(5, 15, 0, 5, 2));
		$this->addAftSystem(new ScatterGun(3, 8, 3, 90, 270));
		$this->addAftSystem(new Bulkhead(0, 2));
		$this->addAftSystem(new Bulkhead(0, 2));

		$this->addLeftSystem(new Thruster(5, 8, 0, 3, 1));
		$this->addLeftSystem(new Thruster(5, 15, 0, 5, 3));
		$this->addLeftSystem(new TwinArray(3, 6, 2, 180, 360));
		$this->addLeftSystem(new TwinArray(3, 6, 2, 180, 360));
		$this->addLeftSystem(new Hangar(5, 12));
		$this->addLeftSystem(new Bulkhead(0, 3));
		$this->addLeftSystem(new Bulkhead(0, 3));

		$this->addRightSystem(new Thruster(5, 8, 0, 3, 1));
		$this->addRightSystem(new Thruster(5, 15, 0, 5, 4));
		$this->addRightSystem(new TwinArray(3, 6, 2, 0, 180));
		$this->addRightSystem(new TwinArray(3, 6, 2, 0, 180));
		$this->addRightSystem(new Hangar(5, 12));
		$this->addRightSystem(new Bulkhead(0, 3));
		$this->addRightSystem(new Bulkhead(0, 3));
        
        $this->addFrontSystem(new Structure( 6, 40));
        $this->addAftSystem(new Structure( 5, 40));
        $this->addLeftSystem(new Structure( 5, 60));
        $this->addRightSystem(new Structure( 5, 60));
        $this->addPrimarySystem(new Structure( 7, 39));
		
		
		$this->hitChart = array(
                0=> array(
                        8 => "Structure",
                        10 => "Scanner",
                        13 => "Engine",
						15 => "Jump Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        2 => "Thruster",
						8 => "Scattergun",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        7 => "Thruster",
						9 => "Scattergun",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
                        8 => "Twin Array",
						11 => "Hangar",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
                        8 => "Twin Array",
						11 => "Hangar",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );

    }
}
?>
