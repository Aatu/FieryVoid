<?php
class zzunoffTashkavaton extends BaseShip{
    /*unofficial - Tashkat with Grav Shifters replaced with Grav Cannons*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 950;
	$this->faction = "Brakiri";
        $this->phpclass = "zzunoffTashkavaton";
        $this->imagePath = "img/ships/tashkat.png"; 
        $this->shipClass = "Tashkavaton Advanced Lance Cruiser";
        $this->variantOf = "Tashkaton Advanced Cruiser";
        $this->occurence = "rare";
        $this->shipSizeClass = 3;

        $this->limited = 33;
		$this->notes = 'Im-Rehsa Technologies';//Corporation producing the design
		$this->notes .= "<br>official Tashkava Advanced Lance Cruiser with Grav Shifters replaced by Grav Cannons"; 
	      $this->isd = 2252;
	      $this->unofficial = true;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        


        $this->turncost = 0.66;
        $this->turndelaycost = 0.5;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 2*5;
        $this->gravitic = true;

        $this->addPrimarySystem(new Reactor(6, 27, 0, 2));
        $this->addPrimarySystem(new CnC(8, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 16, 9, 10));
        $this->addPrimarySystem(new Engine(6, 18, 0, 16, 2));
	$this->addPrimarySystem(new ShieldGenerator(5, 16, 4, 4));
        $this->addPrimarySystem(new JumpEngine(6, 20, 6, 16));
	$this->addPrimarySystem(new Hangar(6, 3));
   

	$this->addFrontSystem(new GraviticShield(0, 6, 0, 3, 240, 0));
	$this->addFrontSystem(new GravitonPulsar(3, 5, 2, 240, 60));
	$this->addFrontSystem(new GravitonPulsar(3, 5, 2, 300, 120));
	$this->addFrontSystem(new GraviticCannon(4, 6, 5, 300, 60));
	$this->addFrontSystem(new GraviticCannon(4, 6, 5, 300, 60));
	$this->addFrontSystem(new GraviticCannon(3, 6, 5, 270, 90));
	$this->addFrontSystem(new GraviticShield(0, 6, 0, 3, 0, 120));
	$this->addFrontSystem(new GraviticThruster(5, 10, 0, 6, 1));
	$this->addFrontSystem(new GraviticThruster(5, 10, 0, 6, 1));
   
	$this->addAftSystem(new GraviticShield(0, 6, 0, 3, 180, 240));
	$this->addAftSystem(new GraviticShield(0, 6, 0, 3, 120, 180));
        $this->addAftSystem(new GravitonPulsar(3, 5, 2, 120, 300));
	$this->addAftSystem(new GravitonPulsar(3, 5, 2, 60, 240));
        $this->addAftSystem(new GravitonBeam(4, 8, 8, 120, 240));
        $this->addAftSystem(new GraviticThruster(5, 15, 0, 8, 2));
        $this->addAftSystem(new GraviticThruster(5, 15, 0, 8, 2));


        $this->addLeftSystem(new GraviticThruster(5, 13, 0, 8, 3));
	$this->addLeftSystem(new GraviticLance(4, 12, 16, 300, 0));
        
        $this->addRightSystem(new GraviticThruster(5, 13, 0, 8, 4));
        $this->addRightSystem(new GraviticLance(4, 12, 16, 0, 60));

        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(5, 46));
        $this->addAftSystem(new Structure(5, 42));
        $this->addLeftSystem(new Structure(5, 48));
        $this->addRightSystem(new Structure(5, 48));
        $this->addPrimarySystem(new Structure(6, 40));
		
		$this->hitChart = array(
			0=> array(
					6 => "Structure",
					8 => "Shield Generator",
					10 => "Jump Engine",
					12 => "Scanner",
					15 => "Engine",
					16 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					3 => "Thruster",
					5 => "Gravitic Shield",
					7 => "Graviton Pulsar",
					10 => "Gravitic Cannon",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					5 => "Thruster",
					7 => "Gravitic Shield",
					9 => "Graviton Pulsar",
					11 => "Graviton Beam",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					6 => "Gravitic Lance",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					6 => "Gravitic Lance",
					18 => "Structure",
					20 => "Primary",
			),
		);
				
    }
}
?>
