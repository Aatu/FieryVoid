<?php
class CrusaderAM extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 490;
        $this->faction = "Orieni";
        $this->phpclass = "CrusaderAM";
        $this->imagePath = "img/ships/commune.png";
        $this->shipClass = "Crusader Heavy Frigate";
        $this->variantOf = "Commune Battle Leader";
            $this->isd = 2007; 

 		$this->unofficial = 'S'; //design released after AoG demise
		
        $this->canvasSize = 100;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus = 65;
        
        $this->occurence = "uncommon";
        $this->limited = 33; //not present on SCS, but for Commune it isn't either - and it's a Limited hull by WotCR book...
         
        
		        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(20); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 20); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
        $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
		//By the Book Orieni should have access to missie types: KK, B, A, H, L, C
		//KK and C missiles are not present in FV however
		
		
        $this->addPrimarySystem(new Reactor(5, 18, 0, 0));
        $this->addPrimarySystem(new CnC(5, 15, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 3, 6));
        $this->addPrimarySystem(new Engine(4, 15, 0, 12, 3));
        $this->addPrimarySystem(new Hangar(1, 1));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 5, 4));        
        
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new HeavyGaussCannon(3, 10, 4, 240, 60));
        $this->addFrontSystem(new HeavyGaussCannon(3, 10, 4, 300, 120));
        $this->addFrontSystem(new RapidGatling(2, 4, 1, 240, 120));
        $this->addFrontSystem(new RapidGatling(2, 4, 1, 240, 120));
		$this->addFrontSystem(new AmmoMissileRackS(5, 0, 0, 270, 90, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base

        $this->addAftSystem(new RapidGatling(2, 4, 1, 120, 360)); 
        $this->addAftSystem(new RapidGatling(2, 4, 1, 0, 240)); 
        $this->addAftSystem(new Thruster(2, 6, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 6, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 6, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 6, 0, 3, 2));        
        
        $this->addPrimarySystem(new Structure(5, 56));
        

        //d20 hit chart
        $this->hitChart = array(
            0=> array(
                8 => "Thruster",
                11 => "Scanner",
                15 => "Engine",
                17 => "Hangar",
                19 => "Reactor",
                20 => "C&C",
            ),
            1=> array(
                5 => "Thruster",
                8 => "Heavy Gauss Cannon",
                10 => "Rapid Gatling Railgun",
                12 => "Class-S Missile Rack",
                17 => "Structure",
                20 => "Primary",
            ),
            2=> array(
                7 => "Thruster",
                9 => "Rapid Gatling Railgun",
                17 => "Structure",
                20 => "Primary",
            ),
        );                
            
        }
    }

?>