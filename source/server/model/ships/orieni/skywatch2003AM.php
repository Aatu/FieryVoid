<?php
class skywatch2003AM extends OSAT
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 550;
		$this->faction = "Orieni";
		$this->phpclass = "skywatch2003AM";
		$this->shipClass = "Skywatch Heavy Orbital Satellite (2003)";
			$this->variantOf = "Skywatch Heavy Orbital Satellite";		
		$this->imagePath = "img/ships/OrieniSkywatchOSAT.png";
		$this->canvasSize = 150;
		$this->isd = 2003;

        $this->fighters = array("medium"=>6);
		$this->notes = "Hunter-killer fighters only.";

		$this->forwardDefense = 12;
		$this->sideDefense = 12;
		
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->accelcost = 0;
		$this->rollcost = 0;
		$this->pivotcost = 0;
		$this->iniativebonus = 60;
		
		
		        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(48); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 48); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
        $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
		//By the Book Orieni should have access to missie types: KK, B, A, H, L, C
		//KK and C missiles are not present in FV however
		
		
		$this->addPrimarySystem(new Reactor(4, 16, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 12, 3, 5));
		$this->addPrimarySystem(new Thruster(4, 14, 0, 0, 2));
        $this->addPrimarySystem(new HKControlNode(4, 12, 1, 1));
		
		$this->addFrontSystem(new AmmoMissileRackSO(5, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new AmmoMissileRackSO(5, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new AmmoMissileRackSO(5, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new AmmoMissileRackSO(5, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
			
        $this->addFrontSystem(new HeavyLaserLance(4, 6, 4, 270, 90));
        $this->addFrontSystem(new HeavyLaserLance(4, 6, 4, 270, 90));
		
		$this->addFrontSystem(new OrieniGatlingRG(1, 4, 1, 180, 360));
		$this->addFrontSystem(new OrieniGatlingRG(1, 4, 1, 0, 360));
		$this->addFrontSystem(new OrieniGatlingRG(1, 4, 1, 0, 360));
		$this->addFrontSystem(new OrieniGatlingRG(1, 4, 1, 0, 180));

		//0:primary, 1:front, 2:rear, 3:left, 4:right;

		$this->addPrimarySystem(new Structure(4, 86));

			$this->hitChart = array(
                0=> array(
					8 => "Structure",
                    10 => "Thruster",
					13 => "1:Class-SO Missile Rack",
					15 => "1:Heavy Laser Lance",
					17 => "1:Gatling Railgun",
					18 => "Scanner",
                    19 => "Reactor",
					20 => "HK Control Node",
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