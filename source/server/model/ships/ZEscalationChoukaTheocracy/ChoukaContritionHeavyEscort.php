<?php
class ChoukaContritionHeavyEscort extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 425;
        $this->faction = "ZEscalation Chouka Theocracy";
        $this->phpclass = "ChoukaContritionHeavyEscort";
        $this->imagePath = "img/ships/EscalationWars/ChoukaPenitent.png";
        $this->shipClass = "Contrition Heavy Escort";
			$this->variantOf = "Penitent Patrol Ship";
			$this->occurence = "rare";		
		$this->unofficial = true;
        $this->canvasSize = 75;
	    $this->isd = 1968;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus = 60;
        
		$this->addPrimarySystem(new Reactor(4, 13, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 15, 6, 6));
        $this->addPrimarySystem(new Engine(4, 13, 0, 8, 4));
        $this->addPrimarySystem(new Hangar(3, 2));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 4));        
        
        $this->addFrontSystem(new HeavyPlasma(3, 8, 5, 300, 60));	
		$this->addFrontSystem(new EWPointPlasmaGun(2, 3, 1, 180, 360));
		$this->addFrontSystem(new EWPointPlasmaGun(2, 3, 1, 180, 360));
		$this->addFrontSystem(new EWHeavyPointPlasmaGun(2, 7, 2, 270, 90));
		$this->addFrontSystem(new EWHeavyPointPlasmaGun(2, 7, 2, 270, 90));
		$this->addFrontSystem(new EWPointPlasmaGun(2, 3, 1, 0, 180));
		$this->addFrontSystem(new EWPointPlasmaGun(2, 3, 1, 0, 180));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
	    
		$this->addAftSystem(new EWHeavyPointPlasmaGun(2, 7, 2, 180, 360));
		$this->addAftSystem(new EWHeavyPointPlasmaGun(2, 7, 2, 0, 180));
        $this->addAftSystem(new Thruster(3, 18, 0, 8, 2));    
       
        $this->addPrimarySystem(new Structure(4, 64));

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
			3 => "Thruster",
			5 => "Heavy Plasma Cannon",
			7 => "Point Plasma Gun",
			10 => "Heavy Point Plasma Gun",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Thruster",
			9 => "Heavy Point Plasma Gun",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
