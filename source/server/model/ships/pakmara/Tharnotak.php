<?php
class Tharnotak extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 775;
		$this->faction = "Pak'ma'ra";
		$this->phpclass = "Tharnotak";
		$this->imagePath = "img/ships/PakmaraTharnotak.png";
		$this->shipClass = "Thar'not'ak Plasma Cruiser";
		$this->shipSizeClass = 3;
	    
        $this->isd = 2248;

		$this->forwardDefense = 14;
		$this->sideDefense = 16;

		$this->turncost = 1;
		$this->turndelaycost = 1;
		$this->accelcost = 3;
		$this->rollcost = 2;
		$this->pivotcost = 4;

		$this->iniativebonus = -1*5;
		  				

		$this->addPrimarySystem(new Reactor(5, 28, 0, 0));
		$this->addPrimarySystem(new ProtectedCnC(6, 14, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 12, 6, 7));
		$this->addPrimarySystem(new Engine(5, 18, 0, 10, 2));
		$this->addPrimarySystem(new Hangar(3, 2));
		$this->addPrimarySystem(new CargoBay(2, 8));
		$this->addPrimarySystem(new CargoBay(2, 8));				

		$this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
		$this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
		$this->addFrontSystem(new PakmaraPlasmaWeb(2, 4, 2, 240, 60));
		$this->addFrontSystem(new PakmaraPlasmaWeb(2, 4, 2, 300, 120));
		$this->addFrontSystem(new MegaPlasma(3, 10, 8, 300, 60));
		$this->addFrontSystem(new HeavyPlasma(3, 8, 5, 300, 60));
		$this->addFrontSystem(new HeavyPlasma(3, 8, 5, 300, 60));				
		$this->addFrontSystem(new PlasmaBattery(2, 2, 0, 2));
		$this->addFrontSystem(new PlasmaBattery(2, 2, 0, 2));	

		$this->addAftSystem(new Thruster(3, 13, 0, 5, 2));
		$this->addAftSystem(new Thruster(3, 13, 0, 5, 2));
		$this->addAftSystem(new MegaPlasma(3, 10, 8, 120, 240));
		$this->addAftSystem(new PakmaraPlasmaWeb(2, 4, 2, 120, 300));
		$this->addAftSystem(new PakmaraPlasmaWeb(2, 4, 2, 60, 240));

		$this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));
		$this->addLeftSystem(new MegaPlasma(5, 10, 8, 240, 360));

		$this->addRightSystem(new Thruster(4, 13, 0, 5, 4));
		$this->addRightSystem(new MegaPlasma(5, 10, 8, 0, 120));

        
        $this->addFrontSystem(new Structure( 5, 40));
        $this->addAftSystem(new Structure( 3, 40));
        $this->addLeftSystem(new Structure( 4, 48));
        $this->addRightSystem(new Structure( 4, 48));
        $this->addPrimarySystem(new Structure( 6, 50));
		
		
		$this->hitChart = array(
                0=> array(
                        8 => "Structure",
                        10 => "Cargo Bay",
                        12 => "Scanner",
                        15 => "Engine",
                        16 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        5 => "Mega Plasma Cannon",
                        7 => "Heavy Plasma Cannon",                        
                        9 => "Plasma Web",
                        11 => "Plasma Battery",                     
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        5 => "Thruster",
                        6 => "Mega Plasma Cannon",
                        8 => "Plasma Web",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
                        7 => "Heavy Plasma Cannon",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
                        7 => "Heavy Plasma Cannon",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );

    }
}
?>
