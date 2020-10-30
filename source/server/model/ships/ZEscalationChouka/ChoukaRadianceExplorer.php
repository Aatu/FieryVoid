<?php
class ChoukaRadianceExplorer extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 480;
	$this->faction = "ZEscalation Chouka";
        $this->phpclass = "ChoukaRadianceExplorer";
        $this->imagePath = "img/ships/EscalationWars/ChoukaRadianceExplorer.png";
        $this->shipClass = "Radiance Explorer";
        $this->shipSizeClass = 3;
		$this->canvasSize = 200; //img has 200px per side
		$this->unofficial = true;
        $this->limited = 10;

        $this->fighters = array("normal"=>6);


	$this->isd = 1936;
        
        $this->forwardDefense = 16;
        $this->sideDefense = 18;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.33;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 5;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(4, 22, 0, 0));
        $this->addPrimarySystem(new CnC(4, 25, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 20, 7, 7));
        $this->addPrimarySystem(new Engine(4, 16, 0, 8, 5));
		$this->addPrimarySystem(new Hangar(3, 15));
		$this->addPrimarySystem(new CargoBay(3, 16));
		$this->addPrimarySystem(new Quarters(3, 16));
		$this->addPrimarySystem(new Quarters(3, 16));
		
        //$this->addFrontSystem(new SWTractorBeam(4, 300, 60, 3));  
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
		$this->addFrontSystem(new EWTwinLaserCannon(2, 8, 4, 300, 60));
		$this->addFrontSystem(new EWTwinLaserCannon(2, 8, 4, 300, 60));
		$this->addFrontSystem(new EWGraviticTractingRod(4, 300, 60, 1));
		$this->addFrontSystem(new CustomIndustrialGrappler(3, 5, 0, 300, 60));
		$this->addFrontSystem(new CustomIndustrialGrappler(3, 5, 0, 300, 60));


        $this->addAftSystem(new Thruster(3, 24, 0, 8, 2));
		$this->addAftSystem(new CargoBay(3, 15));
		$this->addAftSystem(new CargoBay(3, 15));
		$this->addAftSystem(new EWHeavyPointPlasmaGun(2, 7, 2, 180, 360));
		$this->addAftSystem(new EWHeavyPointPlasmaGun(2, 7, 2, 0, 180));


        $this->addLeftSystem(new HeavyPlasma(3, 8, 5, 240, 360));
        $this->addLeftSystem(new HeavyPlasma(3, 8, 5, 240, 360));
        $this->addLeftSystem(new Thruster(3, 15, 0, 4, 3));
		$this->addLeftSystem(new CargoBay(2, 25));
		$this->addLeftSystem(new CargoBay(2, 25));


        $this->addRightSystem(new HeavyPlasma(3, 8, 5, 0, 120));
        $this->addRightSystem(new HeavyPlasma(3, 8, 5, 0, 120));
        $this->addRightSystem(new Thruster(3, 15, 0, 4, 4));
		$this->addRightSystem(new CargoBay(2, 25));
		$this->addRightSystem(new CargoBay(2, 25));


        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 68));
        $this->addAftSystem(new Structure(4, 56));
        $this->addLeftSystem(new Structure(4, 70));
        $this->addRightSystem(new Structure(4, 70));
        $this->addPrimarySystem(new Structure(4, 78));
		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					9 => "Cargo Bay",
					11 => "Quarters",
					13 => "Scanner",
					15 => "Engine",
					18 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					3 => "Thruster",
					6 => "Twin Laser Cannon",
					7 => "Gravitic Tracting Rod",
					9 => "Industrial Grappler",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Heavy Point Plasma Gun",
					11 => "Cargo Bay",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					7 => "Heavy Plasma Cannon",
					11 => "Cargo Bay",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					7 => "Heavy Plasma Cannon",
					11 => "Cargo Bay",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
