<?php
class ChoukaRaiderHeresy extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 280;
        $this->faction = "ZEscalation Chouka Raider";
        $this->phpclass = "ChoukaRaiderHeresy";
        $this->imagePath = "img/ships/EscalationWars/ChoukaRaiderHeresy.png";
        $this->shipClass = "Heresy Wolf Raider";
		$this->unofficial = true;
        $this->canvasSize = 75;
	    $this->isd = 1943;

        $this->fighters = array("normal"=>6);
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = -20;
         
		$this->addPrimarySystem(new Reactor(3, 9, 0, 0));
        $this->addPrimarySystem(new CnC(3, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 4, 4));
        $this->addPrimarySystem(new Engine(3, 10, 0, 6, 2));
        $this->addPrimarySystem(new Hangar(3, 8));
        $this->addPrimarySystem(new Thruster(2, 11, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(2, 11, 0, 3, 4));        
        
		$this->addFrontSystem(new MediumPlasma(2, 5, 3, 240, 360));
		$this->addFrontSystem(new MediumPlasma(2, 5, 3, 0, 120));
        $this->addFrontSystem(new LightPlasma(2, 4, 2, 300, 60));	
		$this->addFrontSystem(new EWPointPlasmaGun(1, 3, 2, 180, 360));
		$this->addFrontSystem(new EWPointPlasmaGun(1, 3, 2, 0, 180));
        $this->addFrontSystem(new Thruster(2, 13, 0, 3, 1));

		$this->addAftSystem(new LightPlasma(1, 4, 2, 180, 300));
		$this->addAftSystem(new LightPlasma(2, 4, 2, 60, 180));
		$this->addAftSystem(new EWPointPlasmaGun(1, 3, 2, 180, 360));
		$this->addAftSystem(new EWPointPlasmaGun(1, 3, 2, 0, 180));
        $this->addAftSystem(new Thruster(2, 18, 0, 6, 2));    
       
        $this->addPrimarySystem(new Structure(3, 56));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			9 => "Thruster",
			12 => "Scanner",
			15 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			5 => "Thruster",
			6 => "Light Plasma Cannon",
			8 => "Medium Plasma Cannon",
			10 => "Point Plasma Gun",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			5 => "Thruster",
			7 => "Point Plasma Gun",
			9 => "Light Plasma Cannon",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
