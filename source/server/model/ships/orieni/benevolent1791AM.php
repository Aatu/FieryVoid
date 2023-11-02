<?php
class Benevolent1791AM extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 600;
		$this->faction = "Orieni";
        $this->phpclass = "Benevolent1791AM";
        $this->imagePath = "img/ships/benevolent.png";
        $this->shipClass = "Benevolent Heavy Scout (early)";
        $this->variantOf = "Benevolent Heavy Scout";
	    $this->isd = 1791;
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
        $ammoMagazine = new AmmoMagazine(12); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 12); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
  //      $this->enhancementOptionsEnabled[] = 'AMMO_C';//add enhancement options for other missiles - Class-C        
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
//		$this->enhancementOptionsEnabled[] = 'AMMO_KK';               
  //      $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
		//By the Book Orieni should have access to missie types: KK, B, A, H, L, C, but L and C not available until 2005 so not disabled on these older ships.

		
		
        $this->addPrimarySystem(new Reactor(5, 24, 0, 0));
        $this->addPrimarySystem(new CnC(6, 25, 0, 0));
        $this->addPrimarySystem(new ElintScanner(5, 25, 8, 8));
        $this->addPrimarySystem(new Engine(5, 25, 0, 9, 4));
        $this->addPrimarySystem(new Hangar(4, 14, 12));
        $this->addPrimarySystem(new JumpEngine(5, 30, 6, 25));
		$this->addPrimarySystem(new AmmoMissileRackSO(5, 0, 0, 0, 360, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        
        $this->addFrontSystem(new LaserLance(2, 6, 4, 240, 60));
        $this->addFrontSystem(new LaserLance(2, 6, 4, 300, 120));
        $this->addFrontSystem(new OrieniGatlingRG(1, 4, 1, 240, 120));
        $this->addFrontSystem(new OrieniGatlingRG(1, 4, 1, 240, 120));
        $this->addFrontSystem(new OrieniGatlingRG(1, 4, 1, 240, 120));
        $this->addFrontSystem(new Thruster(3, 12, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 12, 0, 3, 1));
	    
        $this->addAftSystem(new OrieniGatlingRG(1, 4, 1, 60, 300));
        $this->addAftSystem(new OrieniGatlingRG(1, 4, 1, 60, 300));
        $this->addAftSystem(new Thruster(2, 15, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 15, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 15, 0, 3, 2));
        
        
        $this->addLeftSystem(new OrieniGatlingRG(1, 4, 1, 180, 360));
        $this->addLeftSystem(new OrieniGatlingRG(1, 4, 1, 180, 360));
		$this->addLeftSystem(new Thruster(4, 20, 0, 6, 3));
	    
        $this->addRightSystem(new OrieniGatlingRG(1, 4, 1, 0, 180));
        $this->addRightSystem(new OrieniGatlingRG(1, 4, 1, 0, 180));
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
						17 => "Class-SO Missile Rack",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
						8 => "Laser Lance",
						12 => "Gatling Railgun",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
						6 => "Thruster",
                        9 => "Gatling Railgun",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        5 => "Thruster",
						9 => "Gatling Railgun",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        5 => "Thruster",
						9 => "Gatling Railgun",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );
        
    }
}
?>
