<?php
class StormFront1874AM extends MediumShip{
    /*Orieni StormFront Missile Corvette, variant ISD 1874 - WoCR*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 360;
		$this->faction = "Orieni";
        $this->phpclass = "StormFront1874AM";
        $this->imagePath = "img/ships/stormFront.png";
        $this->shipClass = "Storm Front Missile Corvette (early)";
		$this->variantOf = "Steadfast Escort Corvette";
		$this->isd = 1874;	    
		$this->occurence = "uncommon";
	    
        $this->agile = true;
        $this->canvasSize = 100;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;

        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
		$this->iniativebonus = 60;
		
		
		        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(36); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 36); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
        $this->enhancementOptionsEnabled[] = 'AMMO_C';//add enhancement options for other missiles - Class-C        
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H       
        $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
		//By the Book Orieni should have access to missie types: KK, B, A, H, L, C
		//KK missiles are not present in FV however
		
		
        
         
        $this->addPrimarySystem(new Reactor(4, 15, 0, 2));
        $this->addPrimarySystem(new CnC(4, 15, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 3, 5));
        $this->addPrimarySystem(new Engine(3, 15, 0, 10, 2));
		$this->addPrimarySystem(new Hangar(1, 1));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 5, 3));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 5, 4));        
		
		$this->addFrontSystem(new AmmoMissileRackSO(3, 0, 0, 240, 60, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new AmmoMissileRackSO(3, 0, 0, 270, 90, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new AmmoMissileRackSO(3, 0, 0, 300, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new Thruster(1, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(1, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(1, 6, 0, 2, 1));
	    
        $this->addAftSystem(new OrieniGatlingRG(1, 4, 1, 120, 360));
        $this->addAftSystem(new OrieniGatlingRG(1, 4, 1, 0, 240)); 
        $this->addAftSystem(new Thruster(1, 6, 0, 3, 2));
        $this->addAftSystem(new Thruster(1, 8, 0, 4, 2));
        $this->addAftSystem(new Thruster(1, 6, 0, 3, 2));
        
       
        $this->addPrimarySystem(new Structure(4, 46));
		
	//d20 hit chart
	$this->hitChart = array(
		0=> array( //PRIMARY
			8 => "Thruster",
			11 => "Scanner",
			15 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),
		1=> array( //Fwd
			6 => "Thruster",
			8 => "Class-SO Missile Rack",
			17 => "Structure",
			20 => "Primary",
		),
		2=> array( //Aft
			7 => "Thruster",
			9 => "Gatling Railgun",
			17 => "Structure",
			20 => "Primary",
		),
	); //end of hit chart
		
        }
    }
?>
