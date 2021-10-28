<?php
class ChoukaBrimstoneHeavyOSAT extends OSAT
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 350;
		$this->faction = "ZEscalation Chouka Theocracy";
		$this->phpclass = "ChoukaBrimstoneHeavyOSAT";
		$this->shipClass = "Brimstone Heavy Orbital Satellite";
        $this->imagePath = "img/ships/EscalationWars/ChoukaHellfireOSAT.png";
        $this->canvasSize = 90;
		$this->isd = 1968;
        $this->unofficial = true;

		$this->forwardDefense = 13;
		$this->sideDefense = 13;
		
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->accelcost = 0;
		$this->rollcost = 0;
		$this->pivotcost = 0;
		$this->iniativebonus = 60;
		
		$this->addPrimarySystem(new Reactor(4, 16, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 12, 5, 5));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 0, 2));
		$this->addPrimarySystem(new SMissileRack(3, 6, 0, 240, 360, true));
		$this->addPrimarySystem(new SMissileRack(3, 6, 0, 240, 360, true));
		$this->addPrimarySystem(new EWTwinLaserCannon(2, 8, 4, 270, 90));
		$this->addPrimarySystem(new EWTwinLaserCannon(2, 8, 4, 270, 90));
   		$this->addPrimarySystem(new HeavyPlasma(3, 8, 5, 300, 60)); 
       	$this->addPrimarySystem(new HeavyPlasma(3, 8, 5, 300, 60)); 
		$this->addPrimarySystem(new EWHeavyPointPlasmaGun(2, 7, 2, 0, 360));
		$this->addPrimarySystem(new EWHeavyPointPlasmaGun(2, 7, 2, 0, 360));
		$this->addPrimarySystem(new SMissileRack(3, 6, 0, 0, 120, true));
		$this->addPrimarySystem(new SMissileRack(3, 6, 0, 0, 120, true));

		//0:primary, 1:front, 2:rear, 3:left, 4:right;

		$this->addPrimarySystem(new Structure(4, 60));

			$this->hitChart = array(
                0=> array(
					6 => "Structure",
					8 => "Thruster",
					11 => "Heavy Plasma Cannon",
					13 => "Twin Laser Cannon",
					15 => "Class-S Missile Rack",
					16 => "Heavy Point Plasma Gun",
					18 => "Scanner",
					20 => "Reactor",
                ),
				1=> array(
                        20 => "Primary",
                ),
                2=> array(
                        20 => "Primary",
                ),
        );

	}

}

?>