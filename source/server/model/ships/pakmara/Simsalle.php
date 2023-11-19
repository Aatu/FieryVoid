<?php
class Simsalle extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 400;
		$this->faction = "Pak'Ma'Ra Confederacy";
		$this->phpclass = "Simsalle";
		$this->imagePath = "img/ships/PakmaraSimsalle.png";
		$this->shipClass = "Sim'sall'e Transport Cruiser";
		$this->shipSizeClass = 3;
	    
        $this->isd = 2195;

		$this->forwardDefense = 16;
		$this->sideDefense = 18;

		$this->turncost = 1;
		$this->turndelaycost = 1;
		$this->accelcost = 3;
		$this->rollcost = 3;
		$this->pivotcost = 4;

		$this->iniativebonus = -1*5;

		$this->addPrimarySystem(new Reactor(4, 18, 0, 2));
		$this->addPrimarySystem(new PakmaraCnC(5, 12, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 12, 6, 6));
		$this->addPrimarySystem(new Engine(4, 14, 0, 10, 3));
		$this->addPrimarySystem(new Hangar(2, 4));
		$this->addPrimarySystem(new CargoBay(3, 20));
		$this->addPrimarySystem(new CargoBay(3, 20));				

		$this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
		$this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
		$this->addFrontSystem(new PakmaraPlasmaWeb(2, 4, 2, 180, 360));
		$this->addFrontSystem(new PakmaraPlasmaWeb(2, 4, 2, 0, 180));	
		$this->addFrontSystem(new PlasmaBattery(2, 2, 0, 2)); 
		$this->addFrontSystem(new PlasmaBattery(2, 2, 0, 2));	


		$this->addAftSystem(new Thruster(2, 13, 0, 5, 2));
		$this->addAftSystem(new Thruster(2, 13, 0, 5, 2));
		$this->addAftSystem(new CargoBay(2, 8));
		$this->addAftSystem(new CargoBay(2, 8));
		$this->addAftSystem(new CargoBay(2, 8));		


		$this->addLeftSystem(new Thruster(4, 13, 0, 5, 3));
		$this->addLeftSystem(new HeavyPlasma(3, 8, 5, 240, 360));
		$this->addLeftSystem(new MediumPlasma(2, 5, 3, 180, 360));


		$this->addRightSystem(new Thruster(4, 13, 0, 5, 4));
		$this->addRightSystem(new HeavyPlasma(3, 8, 5, 0, 120));
		$this->addRightSystem(new MediumPlasma(2, 5, 3, 0, 180));

        
        $this->addFrontSystem(new Structure( 4, 36));
        $this->addAftSystem(new Structure( 2, 36));
        $this->addLeftSystem(new Structure( 4, 40));
        $this->addRightSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 5, 30));
		
		
		$this->hitChart = array(
                0=> array(
                        6 => "Structure",
                        10 => "Cargo Bay",
                        12 => "Scanner",
                        15 => "Engine",
                        16 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        5 => "Plasma Battery",                     
                        7 => "Plasma Web",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        5 => "Thruster",
                        9 => "Cargo Bay",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
                        6 => "Heavy Plasma Cannon",
                        8 => "Medium Plasma Cannon",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
                        6 => "Heavy Plasma Cannon",
                        8 => "Medium Plasma Cannon",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );

    }
}
?>
