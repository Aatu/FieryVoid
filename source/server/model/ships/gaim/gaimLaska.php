<?php
class gaimLaska extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 775;
		$this->faction = 'Gaim';
		$this->phpclass = "gaimLaska";
		$this->imagePath = "img/ships/GaimLaska.png";
		$this->shipClass = "Laska Battle Cruiser";
		$this->shipSizeClass = 3;
	    
        $this->isd = 2252;

		$this->forwardDefense = 15;
		$this->sideDefense = 17;

		$this->turncost = 0.66;
		$this->turndelaycost = 0.5;
		$this->accelcost = 3;
		$this->rollcost = 2;
		$this->pivotcost = 3;

		$this->iniativebonus = 10;

		$this->addPrimarySystem(new Reactor(7, 22, 0, 0));
		$this->addPrimarySystem(new CnC(7, 18, 0, 0));
		$this->addPrimarySystem(new Scanner(7, 20, 4, 8));
		$this->addPrimarySystem(new Engine(6, 20, 0, 12, 3));
		$this->addPrimarySystem(new Hangar(7, 2));

		$this->addFrontSystem(new Thruster(6, 10, 0, 3, 1));
		$this->addFrontSystem(new Thruster(6, 10, 0, 3, 1));
		$this->addFrontSystem(new HeavyPulse(4, 6, 4, 300, 60));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 180, 60));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 180, 60));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 300, 180));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 300, 180));
		$this->addFrontSystem(new Bulkhead(0, 3));
		$this->addFrontSystem(new Bulkhead(0, 3));

		$this->addAftSystem(new Thruster(5, 14, 0, 6, 2));
		$this->addAftSystem(new Thruster(5, 14, 0, 6, 2));
		$this->addAftSystem(new JumpEngine(6, 25, 3, 16));
		$this->addAftSystem(new Bulkhead(0, 2));
		$this->addAftSystem(new Bulkhead(0, 2));

		$this->addLeftSystem(new Thruster(5, 15, 0, 5, 3));
		$this->addLeftSystem(new HeavyPulse(4, 6, 4, 240, 360));
		$this->addLeftSystem(new ScatterGun(4, 8, 3, 240, 360));
		$this->addLeftSystem(new Bulkhead(0, 2));
		$this->addLeftSystem(new Bulkhead(0, 2));

		$this->addRightSystem(new Thruster(5, 15, 0, 5, 4));
		$this->addRightSystem(new HeavyPulse(4, 6, 4, 0, 120));
		$this->addRightSystem(new ScatterGun(4, 8, 3, 0, 120));
		$this->addRightSystem(new Bulkhead(0, 2));
		$this->addRightSystem(new Bulkhead(0, 2));
        
        $this->addFrontSystem(new Structure( 6, 44));
        $this->addAftSystem(new Structure( 5, 44));
        $this->addLeftSystem(new Structure( 5, 56));
        $this->addRightSystem(new Structure( 5, 56));
        $this->addPrimarySystem(new Structure( 7, 44));
		
		
		$this->hitChart = array(
                0=> array(
                        9 => "Structure",
                        12 => "Scanner",
                        15 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        3 => "Thruster",
                        5 => "Heavy Pulse Cannon",
                        9 => "Twin Array",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        7 => "Thruster",
                        12 => "Jump Engine",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        3 => "Thruster",
						6 => "Heavy Pulse Cannon",
                        9 => "Scattergun",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                         3 => "Thruster",
						6 => "Heavy Pulse Cannon",
                        9 => "Scattergun",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );

    }
}
?>
