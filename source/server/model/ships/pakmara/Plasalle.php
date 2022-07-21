<?php
class Plasalle extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 550;
		$this->faction = "Pak'ma'ra";
		$this->phpclass = "Plasalle";
		$this->imagePath = "img/ships/PakmaraSimsalle.png";
		$this->shipClass = "Pla'sall'e Wave Cruiser";
		$this->shipSizeClass = 3;
		
			$this->variantOf = "Sim'sall'e Transport Cruiser";
			$this->occurence = "rare";		
	    
        $this->isd = 2231;

		$this->forwardDefense = 16;
		$this->sideDefense = 18;

		$this->turncost = 1;
		$this->turndelaycost = 1;
		$this->accelcost = 3;
		$this->rollcost = 3;
		$this->pivotcost = 4;

		$this->iniativebonus = -1*5;

		$this->addPrimarySystem(new Reactor(4, 18, 0, 0));
		$this->addPrimarySystem(new ProtectedCnC(5, 12, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 12, 6, 6));
		$this->addPrimarySystem(new Engine(4, 14, 0, 10, 3));
		$this->addPrimarySystem(new Hangar(2, 4));
		$this->addPrimarySystem(new CargoBay(3, 20));
		$this->addPrimarySystem(new CargoBay(3, 20));				

		$this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
		$this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
		$this->addFrontSystem(new PlasmaWaveTorpedo(3, 7, 4, 300, 60));		
		$this->addFrontSystem(new PakmaraPlasmaWeb(2, 4, 2, 180, 360));
		$this->addFrontSystem(new PakmaraPlasmaWeb(2, 4, 2, 0, 180));	
		$this->addFrontSystem(new PlasmaBattery(2, 2, 0, 2));//armor, structure, power req, output 
		$this->addFrontSystem(new PlasmaBattery(2, 2, 0, 2));	


		$this->addAftSystem(new Thruster(2, 13, 0, 5, 2));
		$this->addAftSystem(new Thruster(2, 13, 0, 5, 2));
		$this->addAftSystem(new PlasmaWaveTorpedo(3, 7, 4, 120, 240));	
		$this->addAftSystem(new PakmaraPlasmaWeb(2, 4, 2, 180, 360));
		$this->addAftSystem(new PakmaraPlasmaWeb(2, 4, 2, 0, 180));
		$this->addAftSystem(new PlasmaBattery(2, 2, 0, 2));//armor, structure, power req, output 
		$this->addAftSystem(new PlasmaBattery(2, 2, 0, 2));				


		$this->addLeftSystem(new Thruster(4, 13, 0, 5, 3));
		$this->addLeftSystem(new PlasmaWaveTorpedo(3, 7, 4, 240, 360));	
		$this->addLeftSystem(new MediumPlasma(2, 5, 3, 180, 360));


		$this->addRightSystem(new Thruster(4, 13, 0, 5, 4));
		$this->addRightSystem(new PlasmaWaveTorpedo(3, 7, 4, 0, 120));	
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
                        5 => "Plasma Wave",                     
                        6 => "Plasma Battery",
                        8 => "Plasma Web",                        
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        5 => "Thruster",
                        6 => "Plasma Wave",
                        8 => "Plasma Web", 
                        9 => "Plasma Battery",                                                                      18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
                        6 => "Plasma Wave",
                        8 => "Medium Plasma Cannon",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
                        6 => "Plasma Wave",
                        8 => "Medium Plasma Cannon",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );

    }
}
?>
