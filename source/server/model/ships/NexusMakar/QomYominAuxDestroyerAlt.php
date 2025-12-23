<?php
class QomYominAuxDestroyerAlt extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 400;
        $this->faction = "Nexus Makar Federation";
        $this->phpclass = "QomYominAuxDestroyerAlt";
        $this->imagePath = "img/ships/Nexus/makarSreaTormal.png";
		$this->canvasSize = 105; //img has 200px per side
        $this->shipClass = "Srea Tormal Auxiliary Destroyer (Alternate)";
		$this->unofficial = true;
        $this->isd = 1928;

	    $this->notes = '<br>Unreliable Ship:';
 	    $this->notes .= '<br> - Sluggish';
		$this->notes .= '<br>Ramming damage is +33% greater due to large quantities of on board water.';
		$this->notes .= '<br>Alternate design using the thrust-based plasma charge.';
		$this->enhancementOptionsDisabled[] = 'SLUGGISH';

        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 23;

		//Need this to boost the Qom Yomin ramming factor to account for all of the water on board
		//Qom Yomin manned units typically have a +33% to ram
	    $hitBonus = 0; //there is no bonus to hit
	    $rammingAttack = new RammingAttack(0, 0, 360, 130, $hitBonus, true, 0); //actual damage - NOT calculated, but designed (130)
		$this->addPrimarySystem($rammingAttack);
         
        $this->addPrimarySystem(new Reactor(2, 18, 0, 0));
        $this->addPrimarySystem(new CnC(2, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(2, 10, 3, 5));
		$this->addFrontSystem(new NexusPlasmaChargeThrust(2, 7, 4, 300, 60));
        $this->addPrimarySystem(new Engine(2, 13, 0, 7, 4));
        $this->addFrontSystem(new Thruster(1, 13, 0, 4, 1));
        $this->addAftSystem(new Thruster(2, 15, 0, 8, 2));
      
        $this->addLeftSystem(new Thruster(2, 13, 0, 6, 3));
		$this->addLeftSystem(new EWHeavyRocketLauncher(3, 6, 2, 240, 360));
		$this->addLeftSystem(new NexusLightChargeCannon(1, 4, 1, 180, 360));
		$this->addLeftSystem(new NexusLightChargeCannon(1, 4, 1, 180, 360));
		$this->addLeftSystem(new NexusWaterCaster(1, 4, 1, 180, 360));
		$this->addLeftSystem(new Hangar(1, 2));
		
        $this->addRightSystem(new Thruster(2, 13, 0, 6, 4));
		$this->addRightSystem(new EWHeavyRocketLauncher(3, 6, 2, 0, 120));
		$this->addRightSystem(new NexusLightChargeCannon(1, 4, 1, 0, 180));
		$this->addRightSystem(new NexusLightChargeCannon(1, 4, 1, 0, 180));
		$this->addRightSystem(new NexusWaterCaster(1, 4, 1, 0, 180));
		$this->addRightSystem(new Hangar(1, 2));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addLeftSystem(new Structure( 3, 35));
        $this->addRightSystem(new Structure( 3, 35));
        $this->addPrimarySystem(new Structure( 3, 30));
		
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
					10 => "1:Plasma Charge",
                    13 => "2:Thruster",
                    15 => "Scanner",
					17 => "Engine",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            3=> array(
                    5 => "Thruster",
                    7 => "Heavy Rocket Launcher",
                    9 => "Light Charge Cannon",
					11 => "Water Caster",
					12 => "Hangar",
					18 => "Structure",
                    20 => "Primary",
            ),
            4=> array(
                    5 => "Thruster",
                    7 => "Heavy Rocket Launcher",
                    9 => "Light Charge Cannon",
					11 => "Water Caster",
					12 => "Hangar",
					18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
