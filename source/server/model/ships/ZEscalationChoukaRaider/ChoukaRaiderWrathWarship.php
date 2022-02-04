<?php
class ChoukaRaiderWrathWarship extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 180;
        $this->faction = "ZEscalation Chouka Raider";
        $this->phpclass = "ChoukaRaiderWrathWarship";
        $this->imagePath = "img/ships/EscalationWars/ChoukaRaiderWrathWarship.png";
        $this->shipClass = "Wrath Warship";
		$this->unofficial = true;
        $this->canvasSize = 75;
	    $this->isd = 1949;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 4;
        $this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(3, 12, 0, 0));
        $this->addPrimarySystem(new CnC(3, 5, 0, 0));
        $this->addPrimarySystem(new Scanner(2, 6, 3, 3));
        $this->addPrimarySystem(new Engine(3, 9, 0, 6, 5));
        $this->addPrimarySystem(new Hangar(2, 2));
        $this->addPrimarySystem(new Thruster(1, 8, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(1, 8, 0, 4, 4));     
        
		$this->addFrontSystem(new MediumPlasma(1, 5, 3, 240, 360));
		$this->addFrontSystem(new MediumPlasma(1, 5, 3, 0, 120));
        $this->addFrontSystem(new Thruster(2, 8, 0, 4, 1));
	    
		$this->addAftSystem(new LightPlasma(1, 4, 2, 120, 240));
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));    
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));    
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));    
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));    
       
        $this->addPrimarySystem(new Structure(3, 42));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			10 => "Thruster",
			12 => "Scanner",
			15 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			5 => "Thruster",
			8 => "Medium Plasma Cannon",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Thruster",
			8 => "Light Plasma Cannon",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
