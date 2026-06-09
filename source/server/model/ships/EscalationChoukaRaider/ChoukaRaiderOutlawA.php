<?php
class ChoukaRaiderOutlawA extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 275;
        $this->faction = "Escalation Wars Chouka Raider";
        $this->phpclass = "ChoukaRaiderOutlawA";
        $this->imagePath = "img/ships/EscalationWars/ChoukaRaiderHighwaymanCargo.png";
        $this->shipClass = "Outlaw-A Carrier";
			$this->variantOf = "Highwayman-A Sloop";
			$this->occurence = "uncommon";
		$this->unofficial = true;
        $this->canvasSize = 100;
	    $this->isd = 1821;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 10;

        $this->fighters = array("normal"=>6);
        
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
        $this->addPrimarySystem(new Hangar(2, 1, 1));
        $this->addPrimarySystem(new Hangar(1, 3, 3));
        $this->addPrimarySystem(new Hangar(1, 3, 3));     
		$this->addPrimarySystem(new EWPointPlasmaGun(1, 3, 2, 240, 60));
		$this->addPrimarySystem(new EWPointPlasmaGun(1, 3, 2, 300, 120));
        
        $this->addFrontSystem(new MediumPlasma(1, 5, 3, 240, 60));
        $this->addFrontSystem(new MediumPlasma(1, 5, 3, 300, 120));
		$this->addFrontSystem(new EWPointPlasmaGun(1, 3, 2, 270, 90));
		$this->addFrontSystem(new EWPointPlasmaGun(1, 3, 2, 270, 90));
        $this->addFrontSystem(new Thruster(1, 5, 0, 3, 1));
        $this->addFrontSystem(new Thruster(1, 5, 0, 3, 1));
	    
		$this->addAftSystem(new EWPointPlasmaGun(1, 3, 2, 90, 270));
        $this->addAftSystem(new Thruster(1, 7, 0, 5, 2));    
        $this->addAftSystem(new Thruster(1, 7, 0, 5, 2));    
       
        $this->addPrimarySystem(new Structure(3, 42));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			7 => "Thruster",
			9 => "Point Plasma Gun",
			12 => "Scanner",
			4 => "Engine",
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
