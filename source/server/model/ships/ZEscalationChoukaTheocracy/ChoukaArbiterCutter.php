<?php
class ChoukaArbiterCutter extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 250;
        $this->faction = "ZEscalation Chouka Theocracy";
        $this->phpclass = "ChoukaArbiterCutter";
        $this->imagePath = "img/ships/EscalationWars/ChoukaArbiter.png";
        $this->shipClass = "Arbiter Customs Cutter";
		$this->unofficial = true;
        $this->agile = true;
        $this->canvasSize = 75;
	    $this->isd = 1933;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 10;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.5;
        $this->accelcost = 1;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(3, 10, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 4, 4));
        $this->addPrimarySystem(new Engine(3, 9, 0, 6, 1));
		$this->addPrimarySystem(new Quarters(3, 9));
        $this->addPrimarySystem(new Hangar(3, 1));
        $this->addPrimarySystem(new Thruster(1, 11, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(1, 11, 0, 3, 4));        
        
		$this->addFrontSystem(new CustomIndustrialGrappler(2, 5, 0, 300, 60));
		$this->addFrontSystem(new CustomIndustrialGrappler(2, 5, 0, 300, 60));
        $this->addFrontSystem(new LightPlasma(2, 4, 2, 300, 60));
        $this->addFrontSystem(new LightPlasma(2, 4, 2, 300, 60));
		$this->addFrontSystem(new EWPointPlasmaGun(1, 3, 2, 270, 90));
		$this->addFrontSystem(new EWPointPlasmaGun(1, 3, 2, 270, 90));
        $this->addFrontSystem(new Thruster(1, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(1, 6, 0, 2, 1));
	    
		$this->addAftSystem(new EWPointPlasmaGun(1, 3, 2, 180, 360));
		$this->addAftSystem(new EWPointPlasmaGun(1, 3, 2, 0, 180));
        $this->addAftSystem(new Thruster(2, 13, 0, 6, 2));    
       
        $this->addPrimarySystem(new Structure(3, 40));


	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			8 => "Thruster",
			10 => "Quarters",
			13 => "Scanner",
			16 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			3 => "Thruster",
			5 => "Light Plasma Cannon",
			7 => "Point Plasma Gun",
			9 => "Industrial Grappler",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			4 => "Thruster",
			7 => "Point Plasma Gun",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
