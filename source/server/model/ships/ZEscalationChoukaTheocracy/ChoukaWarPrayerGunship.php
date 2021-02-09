<?php
class ChoukaWarPrayerGunship extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 325;
        $this->faction = "ZEscalation Chouka Theocracy";
        $this->phpclass = "ChoukaWarPrayerGunship";
        $this->imagePath = "img/ships/EscalationWars/ChoukaWarPrayer.png";
        $this->shipClass = "War Prayer Gunship";
		$this->unofficial = true;
        $this->agile = true;
        $this->canvasSize = 75;
	    $this->isd = 1952;
        
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
        $this->addPrimarySystem(new Scanner(3, 10, 4, 4));
        $this->addPrimarySystem(new Engine(3, 9, 0, 5, 2));
        $this->addPrimarySystem(new Hangar(2, 1));
        $this->addPrimarySystem(new Thruster(1, 11, 0, 2, 3));
        $this->addPrimarySystem(new Thruster(1, 11, 0, 2, 4));        
        
        $this->addFrontSystem(new HeavyPlasma(2, 8, 5, 300, 60));
        $this->addFrontSystem(new LightPlasma(1, 4, 2, 240, 60));
        $this->addFrontSystem(new LightPlasma(1, 4, 2, 240, 60));
        $this->addFrontSystem(new LightPlasma(1, 4, 2, 300, 120));
        $this->addFrontSystem(new LightPlasma(1, 4, 2, 300, 120));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
	    
		$this->addAftSystem(new LightPlasma(2, 4, 2, 90, 270));
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
			6 => "Heavy Plasma Cannon",
			10 => "Light Plasma Cannon",
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
