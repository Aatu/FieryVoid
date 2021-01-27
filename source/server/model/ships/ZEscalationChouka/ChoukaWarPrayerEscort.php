<?php
class ChoukaWarPrayerEscort extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 275;
        $this->faction = "ZEscalation Chouka";
        $this->phpclass = "ChoukaWarPrayerEscort";
        $this->imagePath = "img/ships/EscalationWars/ChoukaWarPrayer.png";
        $this->shipClass = "War Prayer Escort";
			$this->variantOf = "War Prayer Gunship";
			$this->occurence = "uncommon";
		$this->unofficial = true;
        $this->agile = true;
        $this->canvasSize = 75;
	    $this->isd = 1960;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 13;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 60;
        
         
        $this->addPrimarySystem(new Reactor(3, 12, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 5, 5));
        $this->addPrimarySystem(new Engine(3, 9, 0, 5, 2));
        $this->addPrimarySystem(new Hangar(2, 1));
        $this->addPrimarySystem(new Thruster(1, 11, 0, 2, 3));
        $this->addPrimarySystem(new Thruster(1, 11, 0, 2, 4));        
        
        $this->addFrontSystem(new EWHeavyPointPlasmaGun(2, 7, 2, 270, 90));
        $this->addFrontSystem(new EWPointPlasmaGun(2, 3, 1, 240, 60));
        $this->addFrontSystem(new EWPointPlasmaGun(2, 3, 1, 240, 60));
        $this->addFrontSystem(new EWPointPlasmaGun(2, 3, 1, 300, 120));
        $this->addFrontSystem(new EWPointPlasmaGun(2, 3, 1, 300, 120));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
	    
		$this->addAftSystem(new EWPointPlasmaGun(2, 3, 1, 90, 270));
        $this->addAftSystem(new Thruster(2, 14, 0, 5, 2));    
       
        $this->addPrimarySystem(new Structure(3, 46));


	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			9 => "Thruster",
			12 => "Scanner",
			15 => "Engine",
			16 => "Hangar",
			18 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			4 => "Thruster",
			6 => "Heavy Point Plasma Gun",
			10 => "Point Plasma Gun",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Thruster",
			8 => "Point Plasma Gun",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
