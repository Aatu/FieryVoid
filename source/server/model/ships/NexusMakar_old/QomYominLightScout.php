<?php
class QomYominLightScout extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 330;
        $this->faction = "Nexus Makar Federation (early)";
        $this->phpclass = "QomYominLightScout";
        $this->imagePath = "img/ships/Nexus/makar_sorolma.png";
        $this->shipClass = "Sorol Ma Light Scout";
		$this->unofficial = true;
        $this->canvasSize = 80;
	    $this->isd = 1879;

	    $this->notes = '<br>Unreliable Ship:';
 	    $this->notes .= '<br> - Sluggish';
		$this->notes .= '<br>Ramming damage is +33% greater due to large quantities of on board water.';
		$this->enhancementOptionsDisabled[] = 'SLUGGISH';
        
        $this->forwardDefense = 13;
        $this->sideDefense = 13;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 53;
         
        $this->addPrimarySystem(new Reactor(3, 13, 0, 4));
        $this->addPrimarySystem(new CnC(4, 9, 0, 0));
        $this->addPrimarySystem(new ELINTScanner(3, 12, 5, 5));
        $this->addPrimarySystem(new Engine(3, 11, 0, 5, 3));
		$this->addPrimarySystem(new NexusWaterCaster(3, 4, 1, 0, 360));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 5, 4));   

		//Need this to boost the Qom Yomin ramming factor to account for all of the water on board
		//Qom Yomin manned units typically have a +33% to ram
	    $hitBonus = 0; //there is no bonus to hit
	    $rammingAttack = new RammingAttack(0, 0, 360, 60, $hitBonus, true, 0); //actual damage - NOT calculated, but designed (60)
		$this->addPrimarySystem($rammingAttack);
        
		$this->addFrontSystem(new SensorSpear(3, 6, 3, 180, 60));
		$this->addFrontSystem(new NexusPlasmaCharge(3, 7, 4, 300, 60));
		$this->addFrontSystem(new SensorSpear(3, 6, 3, 300, 180));
        $this->addFrontSystem(new Thruster(3, 7, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 7, 0, 3, 1));
	    
		$this->addAftSystem(new NexusLightChargeCannon(3, 4, 1, 180, 60));
		$this->addAftSystem(new NexusLightChargeCannon(3, 4, 1, 0, 360));
		$this->addAftSystem(new NexusLightChargeCannon(3, 4, 1, 300, 180));
		$this->addAftSystem(new Hangar(2, 2));
		$this->addAftSystem(new Thruster(3, 10, 0, 3, 2));    
        $this->addAftSystem(new Thruster(3, 10, 0, 3, 2));    
        
        $this->addPrimarySystem(new Structure(4, 45));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			8 => "Thruster",
			10 => "Water Caster",
			14 => "ELINT Scanner",
			17 => "Engine",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			6 => "Thruster",
			8 => "Plasma Charge",
			10 => "Sensor Spear",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			7 => "Thruster",
			9 => "Hangar",
			11 => "Light Charge Cannon",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
