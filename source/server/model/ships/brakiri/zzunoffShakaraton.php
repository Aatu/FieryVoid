<?php
class zzunoffShakaraton extends BaseShip{
    /*unofficial - Shakara with Grav Shifters replaced with Grav Cannons*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 700;
	$this->faction = "Brakiri";
        $this->phpclass = "zzunoffShakaraton";
        $this->imagePath = "img/ships/shakara.png"; 
        $this->shipClass = "Shakaraton Scout Cruiser";
        $this->shipSizeClass = 3;

        $this->limited = 10;
	$this->unofficial = true;
	
	
		$this->notes = 'Im-Rehsa Technologies';//Corporation producing the design
		$this->isd = 2251;
        
        
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        


        $this->turncost = 0.66;
        $this->turndelaycost = 0.5;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 2*5;
        $this->gravitic = true;

        $this->addPrimarySystem(new Reactor(5, 22, 0, 3));
        $this->addPrimarySystem(new CnC(6, 16, 0, 0));
        $this->addPrimarySystem(new ElintScanner(5, 20, 9, 12));
        $this->addPrimarySystem(new Engine(5, 16, 0, 14, 3));
	$this->addPrimarySystem(new ShieldGenerator(5, 14, 4, 4));
        $this->addPrimarySystem(new JumpEngine(5, 20, 6, 20));
	$this->addPrimarySystem(new Hangar(5, 3));
   

	
        $this->addFrontSystem(new GravitonPulsar(3, 5, 2, 240, 60));
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 2, 300, 60));
        $this->addFrontSystem(new GravitonPulsar(3, 5, 2, 300, 120));
        $this->addFrontSystem(new GraviticThruster(5, 10, 0, 5, 1));
        $this->addFrontSystem(new GraviticThruster(5, 10, 0, 5, 1));
   
        $this->addAftSystem(new GravitonPulsar(3, 5, 2, 120, 300));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 2, 120, 240));
        $this->addAftSystem(new GravitonPulsar(3, 5, 2, 60, 240));
        $this->addAftSystem(new GraviticThruster(5, 13, 0, 7, 2));
        $this->addAftSystem(new GraviticThruster(5, 13, 0, 7, 2));


	$this->addLeftSystem(new GraviticCannon(4, 6, 5, 240, 0));
        $this->addLeftSystem(new GraviticShield(0, 6, 0, 2, 240, 300));
        $this->addLeftSystem(new GraviticThruster(5, 13, 0, 6, 3));

	$this->addRightSystem(new GraviticCannon(4, 6, 5, 0, 120));
        $this->addRightSystem(new GraviticShield(0, 6, 0, 2, 60, 120));
        $this->addRightSystem(new GraviticThruster(5, 13, 0, 6, 4));

        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(5, 36));
        $this->addAftSystem(new Structure(5, 36));
        $this->addLeftSystem(new Structure(5, 44));
        $this->addRightSystem(new Structure(5, 44));
        $this->addPrimarySystem(new Structure(5, 40));
		
		$this->hitChart = array(
			0=> array(
					6 => "Structure",
					8 => "Shield Generator",
					10 => "Jump Engine",
					13 => "ELINT Scanner",
					16 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					5 => "Gravitic Shield",
					7 => "Graviton Pulsar",
					10 => "Gravitic Cannon",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					5 => "Thruster",
					6 => "Gravitic Shield",
					8 => "Graviton Pulsar",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					5 => "Gravitic Shield",
					7 => "Gravitic Cannon",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					5 => "Gravitic Shield",
					7 => "Gravitic Cannon",
					18 => "Structure",
					20 => "Primary",
			),
		);
				
    }
}
?>
