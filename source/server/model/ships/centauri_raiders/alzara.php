<?php
class Alzara extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 440;
        $this->faction = "Raiders";
        $this->phpclass = "Alzara";
        $this->imagePath = "img/ships/altarian.png";
        $this->shipClass = "Centauri Privateer Alzara Destroyer";
        $this->fighters = array("medium"=>6);
		$this->isd = 2167;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.50;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 30;
        
         
        $this->addPrimarySystem(new Reactor(5, 17, 0, 0));
        $this->addPrimarySystem(new CnC(6, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 13, 6, 6));
        $this->addPrimarySystem(new Engine(5, 15, 0, 10, 3));
        $this->addPrimarySystem(new Hangar(5, 7));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 4));

		$this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
		$this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
		$this->addFrontSystem(new CargoBay(4, 10));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 180, 60));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 300, 180));
		$this->addFrontSystem(new MatterCannon(4, 7, 4, 300, 60));
		
		$this->addAftSystem(new Thruster(4, 14, 0, 5, 2));
		$this->addAftSystem(new Thruster(4, 14, 0, 5, 2));
		$this->addAftSystem(new TwinArray(3, 6, 2, 0, 240));
		$this->addAftSystem(new TwinArray(3, 6, 2, 60, 300));
		$this->addAftSystem(new TwinArray(3, 6, 2, 120, 0));

		//0:primary, 1:front, 2:rear, 3:left, 4:right;
		$this->addFrontSystem(new Structure( 4, 60));
		$this->addAftSystem(new Structure( 4, 60));
		$this->addPrimarySystem(new Structure( 6, 46 ));


		//d20 hit chart
		$this->hitChart = array(
			0=> array(
			6 => "Structure",
			9 => "Thruster",
			12 => "Scanner",
			15 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
			),

			1=> array(
			3 => "Thruster",
			6 => "Matter Cannon",
			8 => "Twin Array",
			9 => "Cargo Bay",
			18 => "Structure",
			20 => "Primary",
			),

			2=> array(
			5 => "Thruster",
			8 => "Twin Array",
			18 => "Structure",
			20 => "Primary",
			),
		);
	}
}
?>