<?php
class VelraxCarytheRefit extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 340;
	$this->faction = "Nexus Velrax Republic";
        $this->phpclass = "VelraxCarytheRefit";
        $this->imagePath = "img/ships/Nexus/velraxVersythe.png";
        $this->shipClass = "Carythe Carrier Support Ship (2108)";
			$this->variantOf = "Versythe Explorer (2108)";
			$this->occurence = "uncommon";
        $this->limited = 10;
	    $this->isd = 2108;
        $this->canvasSize = 125;
		$this->unofficial = true;

        $this->fighters = array("normal"=>12);

        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 30;

        $this->addPrimarySystem(new Reactor(3, 18, 0, 0));
        $this->addPrimarySystem(new CnC(3, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 16, 3, 5));
        $this->addPrimarySystem(new Engine(3, 20, 0, 8, 4));
        $this->addPrimarySystem(new Hangar(2, 2, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 1));
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
		$this->addFrontSystem(new JumpEngine(3, 20, 5, 35));

        $this->addLeftSystem(new DualIonBolter(2, 4, 4, 180, 60));
        $this->addLeftSystem(new DualIonBolter(2, 4, 4, 120, 360));
		$this->addLeftSystem(new Hangar(3, 8, 6));
        $this->addLeftSystem(new Thruster(3, 12, 0, 4, 3));
		$this->addLeftSystem(new CargoBay(2, 30)); 

        $this->addRightSystem(new DualIonBolter(2, 4, 4, 300, 180));
        $this->addRightSystem(new DualIonBolter(2, 4, 4, 0, 240));
		$this->addRightSystem(new Hangar(3, 8, 6));
        $this->addRightSystem(new Thruster(3, 12, 0, 4, 4));
		$this->addRightSystem(new CargoBay(2, 30)); 

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(4, 36));
        $this->addLeftSystem(new Structure(3, 28));
        $this->addRightSystem(new Structure(3, 28));
    
            $this->hitChart = array(
        		0=> array(
        				7 => "Structure",
        				10 => "2:Thruster",
						12 => "1:Jump Engine",
        				14 => "Scanner",
        				16 => "Engine",
						17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		3=> array(
        				4 => "Thruster",
						8 => "Cargo Bay",
        				10 => "Dual Ion Bolter",
						12 => "Hangar",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				4 => "Thruster",
						8 => "Cargo Bay",
        				10 => "Dual Ion Bolter",
						12 => "Hangar",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    
    }
}
?>
