<?php
class ChoukaRaiderHighwaymanA extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 275;
        $this->faction = "ZEscalation Chouka Raider";
        $this->phpclass = "ChoukaRaiderHighwaymanA";
        $this->imagePath = "img/ships/EscalationWars/ChoukaRaiderHighwaymanCargo.png";
        $this->shipClass = "Highwayman-A Sloop";
		$this->unofficial = true;
        $this->canvasSize = 100;
	    $this->isd = 1821;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 10;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 3;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 60;
        
         
        $this->addPrimarySystem(new Reactor(2, 10, 0, 2));
        $this->addPrimarySystem(new CnC(3, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(2, 9, 4, 4));
        $this->addPrimarySystem(new Engine(2, 9, 0, 10, 2));
        $this->addPrimarySystem(new Hangar(2, 1));
        $this->addPrimarySystem(new Thruster(1, 8, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(1, 8, 0, 5, 4));     
		$this->addPrimarySystem(new CargoBay(1, 14));
		$this->addPrimarySystem(new CargoBay(1, 14));
		$this->addPrimarySystem(new EWPointPlasmaGun(1, 3, 1, 240, 60));
		$this->addPrimarySystem(new EWPointPlasmaGun(1, 3, 1, 300, 120));

        
        $this->addFrontSystem(new MediumPlasma(1, 5, 3, 240, 60));
        $this->addFrontSystem(new MediumPlasma(1, 5, 3, 300, 120));
		$this->addFrontSystem(new EWPointPlasmaGun(1, 3, 1, 270, 90));
		$this->addFrontSystem(new EWPointPlasmaGun(1, 3, 1, 270, 90));
        $this->addFrontSystem(new Thruster(1, 5, 0, 3, 1));
        $this->addFrontSystem(new Thruster(1, 5, 0, 3, 1));
	    
		$this->addAftSystem(new EWPointPlasmaGun(1, 3, 1, 90, 270));
        $this->addAftSystem(new Thruster(1, 7, 0, 5, 2));    
        $this->addAftSystem(new Thruster(1, 7, 0, 5, 2));    
       
        $this->addPrimarySystem(new Structure(2, 32));


	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			6 => "Thruster",
			8 => "Point Plasma Gun",
			11 => "Cargo",
			14 => "Scanner",
			16 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			5 => "Thruster",
			7 => "Medium Plasma Cannon",
			9 => "Point Plasma Gun",
			16 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Thruster",
			8 => "Point Plasma Gun",
			16 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
