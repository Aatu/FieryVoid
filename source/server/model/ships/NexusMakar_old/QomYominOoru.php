<?php
class QomYominOoru extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 440;
        $this->faction = "Nexus Makar Federation (early)";
        $this->phpclass = "QomYominOoru";
        $this->imagePath = "img/ships/Nexus/makar_mayol.png";
		$this->canvasSize = 105; //img has 200px per side
        $this->shipClass = "Ooru Drone Destroyer";
			$this->variantOf = "Ma Yol Heavy Destroyer";
			$this->occurence = "common";
		$this->unofficial = true;
        $this->isd = 2053;

        $this->fighters = array("normal"=>12);

	    $this->notes = '<br>Unreliable Ship:';
 	    $this->notes .= '<br> - Sluggish';
		$this->notes .= '<br>Ramming damage is +33% greater due to large quantities of on board water.';
		$this->notes .= '<br>Design using the original plasma charge.';
		$this->enhancementOptionsDisabled[] = 'SLUGGISH';

        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 0.75;
        $this->turndelaycost = 0.75;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 23;

		//Need this to boost the Qom Yomin ramming factor to account for all of the water on board
		//Qom Yomin manned units typically have a +33% to ram
	    $hitBonus = 0; //there is no bonus to hit
	    $rammingAttack = new RammingAttack(0, 0, 360, 175, $hitBonus, true, 0); //actual damage - NOT calculated, but designed (130)
		$this->addPrimarySystem($rammingAttack);
         
        $this->addPrimarySystem(new Reactor(4, 20, 0, 4));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 3, 6));
		$this->addFrontSystem(new HKControlNode(3, 12, 2, 2));
        $this->addPrimarySystem(new Engine(4, 18, 0, 6, 3));
        $this->addAftSystem(new Thruster(3, 13, 0, 6, 1));
        $this->addAftSystem(new Thruster(3, 15, 0, 8, 2));
		$this->addPrimarySystem(new Hangar(2, 8));
		
        $this->addLeftSystem(new Thruster(3, 15, 0, 6, 3));
		$this->addLeftSystem(new EWHeavyRocketLauncher(3, 6, 2, 300, 60));
		$this->addLeftSystem(new NexusLightChargeCannon(3, 4, 1, 180, 360));
		$this->addLeftSystem(new NexusLightChargeCannon(3, 4, 1, 180, 360));
		$this->addLeftSystem(new NexusLightChargeCannon(3, 4, 1, 180, 360));
		$this->addLeftSystem(new NexusWaterCaster(1, 4, 1, 180, 360));
		
        $this->addRightSystem(new Thruster(3, 15, 0, 6, 4));
		$this->addRightSystem(new EWHeavyRocketLauncher(3, 6, 2, 300, 60));
		$this->addRightSystem(new NexusLightChargeCannon(3, 4, 1, 0, 180));
		$this->addRightSystem(new NexusLightChargeCannon(3, 4, 1, 0, 180));
		$this->addRightSystem(new NexusLightChargeCannon(3, 4, 1, 0, 180));
		$this->addRightSystem(new NexusWaterCaster(1, 4, 1, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addLeftSystem(new Structure( 4, 48));
        $this->addRightSystem(new Structure( 4, 48));
        $this->addPrimarySystem(new Structure( 4, 40));
		
        $this->hitChart = array(
            0=> array(
                    7 => "Structure",
					8 => "Hangar",
					10 => "1:HK Control Node",
                    13 => "2:Thruster",
                    15 => "Scanner",
					17 => "Engine",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            3=> array(
                    4 => "Thruster",
                    6 => "RAM Launcher",
                    9 => "Light Charge Cannon",
					11 => "Water Caster",
					18 => "Structure",
                    20 => "Primary",
            ),
            4=> array(
                    4 => "Thruster",
                    6 => "RAM Launcher",
                    9 => "Light Charge Cannon",
					11 => "Water Caster",
					18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
