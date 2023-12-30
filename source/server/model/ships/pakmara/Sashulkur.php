<?php
class Sashulkur extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 600;
		$this->faction = "Pak'ma'ra Confederacy";
		$this->phpclass = "Sashulkur";
		$this->imagePath = "img/ships/PakmaraReshkasu.png";
		$this->shipClass = "Sashul'kur Blockade Runner";
		$this->shipSizeClass = 3;
		
			$this->variantOf = "Resh'kas'u Light Carrier";
			$this->occurence = "uncommon";			
	    
        $this->isd = 2224;

		$this->forwardDefense = 16;
		$this->sideDefense = 16;

		$this->turncost = 1;
		$this->turndelaycost = 1;
		$this->accelcost = 3;
		$this->rollcost = 2;
		$this->pivotcost = 4;

		$this->iniativebonus = -1*5;
		
  				

		/* let's replace this with appropriate two C&Cs!
		$this->addPrimarySystem(new PakmaraCnC(6, 12, 0, 0));
		*/
		$cnc = new PakmaraCnC(5, 6, 0, 0);
		$cnc->startArc = 0;
		$cnc->endArc = 360;
        $this->addPrimarySystem($cnc);
		$cnc = new SecondaryCnC(5, 6, 0, 0);
		$cnc->startArc = 0;
		$cnc->endArc = 360;
        $this->addPrimarySystem($cnc);
		
		$this->addPrimarySystem(new Reactor(5, 23, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 12, 6, 7));
		$this->addPrimarySystem(new Engine(5, 18, 0, 12, 3));
		$this->addPrimarySystem(new JumpEngine(4, 15, 4, 48));
		$this->addPrimarySystem(new Hangar(3, 3));
		$this->addPrimarySystem(new Quarters(2, 9));		

		$this->addFrontSystem(new Thruster(4, 13, 0, 4, 1));
		$this->addFrontSystem(new Thruster(4, 13, 0, 4, 1));
		$this->addFrontSystem(new PakmaraPlasmaWeb(2, 4, 2, 240, 60));
		$this->addFrontSystem(new PakmaraPlasmaWeb(2, 4, 2, 300, 120));
		$this->addFrontSystem(new PlasmaAccelerator(4, 10, 5, 300, 60));
		$this->addFrontSystem(new PlasmaBattery(2, 2, 0, 2));
		$this->addFrontSystem(new PlasmaBattery(2, 2, 0, 2));	


		$this->addAftSystem(new Thruster(3, 15, 0, 6, 2));
		$this->addAftSystem(new Thruster(3, 15, 0, 6, 2));
		$this->addAftSystem(new PlasmaStream(3, 9, 7, 120, 240));
		$this->addAftSystem(new PakmaraPlasmaWeb(2, 4, 2, 120, 300));
		$this->addAftSystem(new PakmaraPlasmaWeb(2, 4, 2, 60, 240));


		$this->addLeftSystem(new Thruster(4, 13, 0, 5, 3));
		$this->addLeftSystem(new PlasmaStream(3, 9, 7, 300, 60));
		$this->addLeftSystem(new CargoBay(2, 16));


		$this->addRightSystem(new Thruster(4, 13, 0, 5, 4));
		$this->addRightSystem(new PlasmaStream(3, 9, 7, 300, 60));
		$this->addRightSystem(new CargoBay(2, 16));

        
        $this->addFrontSystem(new Structure( 4, 36));
        $this->addAftSystem(new Structure( 3, 36));
        $this->addLeftSystem(new Structure( 4, 44));
        $this->addRightSystem(new Structure( 4, 44));
        $this->addPrimarySystem(new Structure( 5, 36));
		
		
		$this->hitChart = array(
                0=> array(
                        7 => "Structure",
						8 => "Quarters",                        
                        10 => "Jump Engine",
                        12 => "Scanner",
                        14 => "Engine",
                        16 => "Hangar",
                        19 => "Reactor",
                        20 => "TAG:C&C",
                ),
                1=> array(
                        3 => "Thruster",
                        4 => "Plasma Battery",                     
                        6 => "Plasma Accelerator",
                        8 => "Plasma Web",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        5 => "Thruster",
                        7 => "Plasma Stream",
                        9 => "Plasma Web",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
                        5 => "Plasma Stream",
						8 => "Cargo Bay",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
                        5 => "Plasma Stream",
						8 => "Cargo Bay",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );

    }
}
?>
