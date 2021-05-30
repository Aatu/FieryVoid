<?php
class PrivateerLessara extends BaseShip{

    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 490;
        $this->faction = "Raiders";
        $this->phpclass = "PrivateerLessara";
        $this->imagePath = "img/ships/PrivateerLessara.png";
        $this->shipClass = "Centauri Privateer Lessara Carrier";
        $this->shipSizeClass = 3;
        $this->fighters = array("light"=>18);
		$this->isd = 2124;
        
		$this->notes = "Used only by Centauri Privateers";

        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 4;
        $this->pivotcost = 4;       
         
        $this->addPrimarySystem(new Reactor(5, 17, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 9, 3, 6));
        $this->addPrimarySystem(new Engine(5, 20, 0, 8, 4));	
		$this->addPrimarySystem(new Hangar(5, 20));	
        
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new CargoBay(4, 20));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
        $this->addFrontSystem(new CargoBay(4, 20));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 180, 60));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 240, 120));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 300, 180));
		
        $this->addAftSystem(new Thruster(4, 13, 0, 5, 2));
        $this->addAftSystem(new Thruster(4, 13, 0, 5, 2));
        $this->addAftSystem(new JumpEngine(4, 20, 5, 48));
        $this->addAftSystem(new TwinArray(3, 6, 2, 120, 0));
        $this->addAftSystem(new TwinArray(3, 6, 2, 0, 240));
        
		$this->addLeftSystem(new Thruster(4, 15, 0, 4, 3));
        $this->addLeftSystem(new MediumPlasma(3, 5, 3, 240, 60));

    	$this->addRightSystem(new Thruster(4, 15, 0, 4, 4));
        $this->addRightSystem(new MediumPlasma(3, 5, 3, 300, 120));     
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(5, 44));
        $this->addAftSystem(new Structure(5, 44));
        $this->addLeftSystem(new Structure(5, 48));
        $this->addRightSystem(new Structure(5, 48));
        $this->addPrimarySystem(new Structure(5, 44));
		


	//d20 hit chart
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
			6 => "Twin Array",
			9 => "Cargo Bay",
			10 => "Medium Plasma Cannon",
			18 => "Structure",
			20 => "Primary",
		),

		2=> array(
			7 => "Thruster",
			9 => "Jump Engine",
			11 => "Twin Array",
			18 => "Structure",
			20 => "Primary",
		),

		3=> array(
			6 => "Thruster",
			8 => "Medium Plasma Cannon",
			18 => "Structure",
			20 => "Primary",
		),

		4=> array(
			6 => "Thruster",
			8 => "Medium Plasma Cannon",
			18 => "Structure",
			20 => "Primary",
		),

	);

		
    }
}
?>