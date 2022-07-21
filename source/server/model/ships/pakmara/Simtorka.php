<?php
class Simtorka extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 500;
		$this->faction = "Pak'ma'ra";
		$this->phpclass = "Simtorka";
		$this->imagePath = "img/ships/PakmaraSimsalle.png";
		$this->shipClass = "Sim'tor'ka Survey Transport";
		$this->shipSizeClass = 3;
		
			$this->variantOf = "Sim'sall'e Transport Cruiser";
			$this->occurence = "rare";		
	    
        $this->isd = 2190;

		$this->forwardDefense = 16;
		$this->sideDefense = 18;

		$this->turncost = 1;
		$this->turndelaycost = 1;
		$this->accelcost = 3;
		$this->rollcost = 3;
		$this->pivotcost = 4;

		$this->iniativebonus = -1*5;

		$this->addPrimarySystem(new Reactor(4, 18, 0, 4));
		$this->addPrimarySystem(new JumpEngine(4, 15, 4, 48));		
		$this->addPrimarySystem(new PakmaraCnC(5, 12, 0, 0));
		$this->addPrimarySystem(new ElintScanner(4, 20, 6, 11));
		$this->addPrimarySystem(new Engine(4, 14, 0, 10, 3));
		$this->addPrimarySystem(new Hangar(2, 8));
		$this->addPrimarySystem(new CargoBay(3, 20));
		$this->addPrimarySystem(new CargoBay(3, 20));				

		$this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
		$this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
		$this->addFrontSystem(new PakmaraPlasmaWeb(2, 4, 2, 270, 90));	
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
		$this->addLeftSystem(new MediumPlasma(3, 5, 3, 240, 360));
		$this->addLeftSystem(new MediumPlasma(2, 5, 3, 180, 360));


		$this->addRightSystem(new Thruster(4, 13, 0, 5, 4));
		$this->addRightSystem(new MediumPlasma(3, 5, 3, 0, 120));
		$this->addRightSystem(new MediumPlasma(2, 5, 3, 0, 180));

        
        $this->addFrontSystem(new Structure( 4, 36));
        $this->addAftSystem(new Structure( 2, 36));
        $this->addLeftSystem(new Structure( 4, 40));
        $this->addRightSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 5, 30));
		
		
		$this->hitChart = array(
                0=> array(
                        5 => "Structure",
                        8 => "Cargo Bay",
                        10 => "Jump Engine",                       
                        12 => "Scanner",
                        15 => "Engine",
                        16 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        5 => "Plasma Battery",
                        8 => "Plasma Web",                        
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        5 => "Thruster",
                        9 => "Cargo Bay",                                                                     18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
                        8 => "Medium Plasma Cannon",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
                        8 => "Medium Plasma Cannon",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );

    }
}
?>
