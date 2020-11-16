<?php
class gaimShamor extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 750;
		$this->faction = 'Gaim';
		$this->phpclass = "gaimShamor";
		$this->imagePath = "img/ships/gaimShamor.png";
		$this->shipClass = "Shamor Battle Scout";
		$this->shipSizeClass = 3;
			$this->limited = 33;
		$this->fighters = array("light"=>12, "medium"=>6);
	    
        $this->isd = 2257;

		$this->forwardDefense = 16;
		$this->sideDefense = 18;

		$this->turncost = 1;
		$this->turndelaycost = 1;
		$this->accelcost = 3;
		$this->rollcost = 3;
		$this->pivotcost = 3;

		$this->iniativebonus = -5;

		$this->addPrimarySystem(new Reactor(4, 20, 0, 0));
		$this->addPrimarySystem(new CnC(4, 6, 0, 0));
		$this->addPrimarySystem(new CnC(4, 6, 0, 0));
		$this->addPrimarySystem(new ELINTScanner(5, 20, 9, 10));
		$this->addPrimarySystem(new Engine(4, 14, 0, 10, 3));
		$this->addPrimarySystem(new Hangar(2, 9));

		$this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
		$this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
		$this->addFrontSystem(new TwinArray(2, 6, 2, 180, 360));
		$this->addFrontSystem(new TwinArray(2, 6, 2, 0, 180));
		$this->addFrontSystem(new TwinArray(2, 6, 2, 270, 90));
		$this->addFrontSystem(new Bulkhead(0, 3));
		$this->addFrontSystem(new Bulkhead(0, 3));

		$this->addAftSystem(new Thruster(4, 13, 0, 5, 2));
		$this->addAftSystem(new Thruster(4, 13, 0, 5, 2));
		$this->addAftSystem(new TwinArray(2, 6, 2, 90, 270));
		$this->addAftSystem(new Hangar(3, 6));
		$this->addAftSystem(new Hangar(3, 6));
		$this->addAftSystem(new Bulkhead(0, 2));
		$this->addAftSystem(new Bulkhead(0, 2));

		$this->addLeftSystem(new Thruster(4, 13, 0, 5, 3));
		$this->addLeftSystem(new BattleLaser(3, 6, 6, 240, 360));
		$this->addLeftSystem(new ScatterGun(2, 8, 3, 180, 360));
		$this->addLeftSystem(new Bulkhead(0, 4));
		$this->addLeftSystem(new Bulkhead(0, 4));

		$this->addRightSystem(new Thruster(4, 13, 0, 5, 4));
		$this->addRightSystem(new BattleLaser(3, 6, 6, 0, 120));
		$this->addRightSystem(new ScatterGun(2, 8, 3, 0, 180));
		$this->addRightSystem(new Bulkhead(0, 4));
		$this->addRightSystem(new Bulkhead(0, 4));
        
        $this->addFrontSystem(new Structure( 4, 36));
        $this->addAftSystem(new Structure( 3, 36));
        $this->addLeftSystem(new Structure( 4, 40));
        $this->addRightSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 5, 30));
		
		
		$this->hitChart = array(
                0=> array(
                        8 => "Structure",
                        11 => "ELINT Scanner",
                        14 => "Engine",
                        16 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        7 => "Twin Array",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        5 => "Thruster",
                        9 => "Hangar",
						10 => "Twin Array",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
						6 => "Battle Laser",
						8 => "Scattergun",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
						6 => "Battle Laser",
						8 => "Scattergun",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );

    }
}
?>
