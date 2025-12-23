<?php
class ColonialTigerClassAM extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
         $this->pointCost = 800;
        $this->faction = "BSG Colonials";
        $this->phpclass = "ColonialTigerClassAM";
        $this->imagePath = "img/ships/BSG/ColonialTiger.png";
        $this->shipClass = "Tiger Gunstar";
        $this->fighters = array("normal" => 6, "superheavy" => 1);
 //       $this->isd = 2160;
        $this->canvasSize = 145;

		$this->unofficial = true;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 17;

        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        
        $this->iniativebonus = 35;

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(40); //pass magazine capacity - 20 rounds per launcher, plus reload rack 80
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 40); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
        $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-F
        $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
        $this->enhancementOptionsEnabled[] = 'AMMO_P';//add enhancement options for other missiles - Class-P

        $this->addPrimarySystem(new Reactor(5, 18, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 6, 7));
        $this->addPrimarySystem(new Engine(5, 14, 0, 10, 3));
        $this->addPrimarySystem(new Hangar(4, 7));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 5, 4));
        $this->addPrimarySystem(new BSGFlakBattery(4, 6, 2, 0, 360));
        $hyperdrive = new JumpEngine(3, 12, 6, 20);
			$hyperdrive->displayName = 'Hyperdrive';
			$this->addPrimarySystem($hyperdrive);
        
        $this->addFrontSystem(new Thruster(5, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(5, 8, 0, 3, 1));		
		$this->addFrontSystem(new BSGMainBattery(5, 9, 6, 330, 30));
		$this->addFrontSystem(new BSGMedBattery(5, 9, 4, 300, 360)); 
		$this->addFrontSystem(new BSGMedBattery(5, 9, 4, 330, 30)); 
        $this->addFrontSystem(new BSGMedBattery(5, 9, 4, 0, 60)); 
        $this->addFrontSystem(new AmmoMissileRackS(5, 0, 0, 270, 360, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new AmmoMissileRackS(5, 0, 0, 0, 90, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new Bulkhead(0, 5));
		$this->addFrontSystem(new Bulkhead(0, 5));

        $this->addAftSystem(new Thruster(5, 12, 0, 6, 2));
        $this->addAftSystem(new Thruster(5, 12, 0, 6, 2));
		$this->addAftSystem(new BSGMedBattery(5, 7, 4, 180, 240));
		$this->addAftSystem(new BSGMedBattery(5, 9, 4, 150, 210));
		$this->addAftSystem(new BSGMedBattery(5, 7, 4, 120, 180));		
        $this->addAftSystem(new Bulkhead(0, 5));		
        $this->addAftSystem(new AmmoMissileRackS(5, 0, 0, 120, 240, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base		
		$this->addAftSystem(new RapidGatling(4, 4, 1, 180, 360)); 
		$this->addAftSystem(new RapidGatling(4, 4, 1, 180, 360)); 
		$this->addAftSystem(new RapidGatling(4, 4, 1, 0, 180)); 
		$this->addAftSystem(new RapidGatling(4, 4, 1, 0, 180)); 

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 50));
        $this->addAftSystem(new Structure( 5, 50));
        $this->addPrimarySystem(new Structure( 5, 60));
		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					9 => "Flak Battery",					
					11 => "Thruster",
					13 => "Scanner",
					15 => "Engine",
					16 => "Hangar",
					17 => "Hyperdrive",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					5 => "Thruster",
					8 => "Battery",
					10 => "Class-S Missile Rack",
                    18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					5 => "Thruster",
					8 => "Battery",
                    9 => "Class-S Missile Rack",
                    11 => "Rapid Gatling Railgun",
					18 => "Structure",
					20 => "Primary",
			),
		);
		

    }
}
?>
