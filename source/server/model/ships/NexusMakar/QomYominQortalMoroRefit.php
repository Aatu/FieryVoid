<?php
class QomYominQortalMoroRefit extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 660;
	$this->faction = "Nexus Makar Federation";
        $this->phpclass = "QomYominQortalMoroRefit";
        $this->imagePath = "img/ships/Nexus/makar_qortalmoro2.png";
        $this->shipClass = "Qortal Moro Explorer (2109)";
        $this->shipSizeClass = 3;
		$this->canvasSize = 175; 
		$this->unofficial = true;
        $this->limited = 10;

		$this->isd = 2109;

	    $this->notes = '<br>Unreliable Ship:';
 	    $this->notes .= '<br> - Sluggish';
		$this->notes .= '<br>Ramming damage is +33% greater due to large quantities of on board water.';
		$this->enhancementOptionsDisabled[] = 'SLUGGISH';

        $this->forwardDefense = 17;
        $this->sideDefense = 17;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 4;
        $this->rollcost = 4;
        $this->pivotcost = 4;
        $this->iniativebonus = -7;
        
        $this->addPrimarySystem(new Reactor(4, 25, 0, 2));
        $this->addPrimarySystem(new CnC(4, 20, 0, 0));
 		$this->addPrimarySystem(new ELINTScanner(4, 16, 6, 9));
		$this->addPrimarySystem(new Hangar(2, 4));
        $this->addPrimarySystem(new Engine(4, 23, 0, 6, 3));

		//Need this to boost the Qom Yomin ramming factor to account for all of the water on board
		//Qom Yomin manned units typically have a +33% to ram
	    $hitBonus = 0; //there is no bonus to hit
	    $rammingAttack = new RammingAttack(0, 0, 360, 305, $hitBonus, true, 0); //actual damage - NOT calculated, but designed (60)
		$this->addPrimarySystem($rammingAttack);
		
        $this->addFrontSystem(new Thruster(3, 15, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 15, 0, 4, 1));
		$this->addFrontSystem(new NexusLightBurstBeam(3, 4, 3, 180, 60));
		$this->addFrontSystem(new NexusLightChargeCannon(3, 4, 1, 240, 60));
		$this->addFrontSystem(new NexusRAMLauncher(3, 8, 4, 300, 60));
		$this->addFrontSystem(new NexusRAMLauncher(3, 8, 4, 300, 60));
		$this->addFrontSystem(new NexusLightChargeCannon(3, 4, 1, 300, 120));
		$this->addFrontSystem(new NexusLightBurstBeam(3, 4, 3, 300, 180));

        $this->addAftSystem(new Thruster(3, 15, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 4, 2));
		$this->addAftSystem(new NexusLightChargeCannon(3, 4, 1, 120, 300));
		$this->addAftSystem(new JumpEngine(3, 20, 4, 35));
		$this->addAftSystem(new NexusLightChargeCannon(3, 4, 1, 60, 240));

		$this->addLeftSystem(new Thruster(3, 15, 0, 5, 3));
		$this->addLeftSystem(new CargoBay(2, 12));
		$this->addLeftSystem(new NexusLightBurstBeam(3, 4, 3, 180, 360));
		$this->addLeftSystem(new NexusWaterCaster(3, 4, 1, 180, 360));
		$this->addLeftSystem(new NexusLightChargeCannon(3, 4, 1, 180, 360));
		$this->addLeftSystem(new NexusPlasmaCharge(3, 7, 4, 240, 360));

		$this->addRightSystem(new Thruster(3, 15, 0, 5, 4));
		$this->addRightSystem(new CargoBay(2, 12));
		$this->addRightSystem(new NexusLightBurstBeam(3, 4, 3, 0, 180));
		$this->addRightSystem(new NexusWaterCaster(3, 4, 1, 0, 180));
		$this->addRightSystem(new NexusLightChargeCannon(3, 4, 1, 0, 180));
		$this->addRightSystem(new NexusPlasmaCharge(3, 7, 4, 0, 120));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 46));
        $this->addAftSystem(new Structure(4, 40));
        $this->addLeftSystem(new Structure(4, 50));
        $this->addRightSystem(new Structure(4, 50));
        $this->addPrimarySystem(new Structure(4, 42));
		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					10 => "Hangar",
					13 => "ELINT Scanner",
					17 => "Engine",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					6 => "RAM Launcher",
					8 => "Light Burst Beam",
					10 => "Light Charge Cannon",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Jump Engine",
					10 => "Light Charge Cannon",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					6 => "Plasma Charge",
					7 => "Water Caster",
					8 => "Light Burst Beam",
					9 => "Cargo Bay",
					11 => "Light Charge Cannon",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					6 => "Plasma Charge",
					7 => "Water Caster",
					8 => "Light Burst Beam",
					9 => "Cargo Bay",
					11 => "Light Charge Cannon",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
