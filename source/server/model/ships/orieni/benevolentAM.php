<?php
class BenevolentAM extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 660;
		$this->faction = "Orieni";
        $this->phpclass = "BenevolentAM";
        $this->imagePath = "img/ships/benevolent.png";
        $this->shipClass = "Benevolent Heavy Scout";
	    $this->isd = 2007;
        $this->shipSizeClass = 3;
        $this->fighters = array("light"=>6, "medium"=>6);
        $this->canvasSize = 200;
		
        $this->forwardDefense = 16;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 3;

        $this->limited = 33;

		
		//ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(20); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 20); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
        $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
		//By the Book Orieni should have access to missie types: KK, B, A, H, L, C
		//KK and C missiles are not present in FV however
		
		
        $this->addPrimarySystem(new Reactor(5, 24, 0, 0));
        $this->addPrimarySystem(new CnC(6, 25, 0, 0));
        $this->addPrimarySystem(new ElintScanner(5, 25, 8, 9));
        $this->addPrimarySystem(new Engine(5, 25, 0, 9, 4));
        $this->addPrimarySystem(new Hangar(4, 14, 12));
        $this->addPrimarySystem(new JumpEngine(5, 30, 6, 25));
		$this->addPrimarySystem(new AmmoMissileRackS(5, 0, 0, 0, 360, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        
        $this->addFrontSystem(new HeavyLaserLance(3, 6, 4, 240, 60));
        $this->addFrontSystem(new RapidGatling(2, 4, 1, 240, 120));
        $this->addFrontSystem(new RapidGatling(2, 4, 1, 240, 120));
        $this->addFrontSystem(new RapidGatling(2, 4, 1, 240, 120));
        $this->addFrontSystem(new HeavyLaserLance(3, 6, 4, 300, 120));
        $this->addFrontSystem(new Thruster(3, 12, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 12, 0, 3, 1));

        $this->addAftSystem(new RapidGatling(1, 4, 1, 60, 300));
        $this->addAftSystem(new RapidGatling(1, 4, 1, 60, 300));
        $this->addAftSystem(new Thruster(3, 15, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 3, 2));
        
        $this->addLeftSystem(new RapidGatling(2, 4, 1, 180, 360));
        $this->addLeftSystem(new RapidGatling(2, 4, 1, 180, 360));
        $this->addLeftSystem(new Thruster(4, 20, 0, 6, 3));

        $this->addRightSystem(new RapidGatling(2, 4, 1, 0, 180));
        $this->addRightSystem(new RapidGatling(2, 4, 1, 0, 180));
        $this->addRightSystem(new Thruster(4, 20, 0, 6, 4));

		//structures
        $this->addFrontSystem(new Structure(4, 48));
        $this->addAftSystem(new Structure(4, 44));
        $this->addLeftSystem(new Structure(4, 45));
        $this->addRightSystem(new Structure(4, 45));
        $this->addPrimarySystem(new Structure(5, 56));
	    
	$this->hitChart = array(
                0=> array(
                        7 => "Structure",
						9 => "Jump Engine",
                        12 => "ELINT Scanner",
                        14 => "Engine",
						16 => "Hangar",
						17 => "Class-S Missile Rack",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
						8 => "Heavy Laser Lance",
						12 => "Rapid Gatling Railgun",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
						6 => "Thruster",
                        9 => "Rapid Gatling Railgun",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        5 => "Thruster",
						9 => "Rapid Gatling Railgun",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        5 => "Thruster",
						9 => "Rapid Gatling Railgun",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );
        
    }

}



?>
