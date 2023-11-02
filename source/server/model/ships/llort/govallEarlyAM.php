<?php
class govallEarlyAM extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 700;
        $this->faction = "Llort";
        $this->phpclass = "govallEarlyAM";
        $this->imagePath = "img/ships/LlortGovall.png";
        $this->shipClass = "Govall Bombardment Destroyer (Early)";
        
		$this->variantOf = "Govall Bombardment Destroyer";
		$this->occurence = "common";        
 
        $this->limited = 33; 
        $this->isd = 2241;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;
        
        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(100); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 140); //add full load of basic missiles
	    $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
	    $this->enhancementOptionsEnabled[] = 'AMMO_C';//add enhancement options for other missiles - Class-C
	    $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-F
	    $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
	    $this->enhancementOptionsEnabled[] = 'AMMO_K';//add enhancement options for other missiles - Class-K   
	    $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
	    $this->enhancementOptionsEnabled[] = 'AMMO_M';//add enhancement options for other missiles - Class-M	    
		$this->enhancementOptionsEnabled[] = 'AMMO_P';//add enhancement options for other missiles - Class-P
		
        $this->addPrimarySystem(new Reactor(4, 15, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 4, 6));
	$this->addPrimarySystem(new Engine(4, 12, 0, 8, 2));
        $this->addPrimarySystem(new Thruster(3, 8, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 5, 4));

        $this->addFrontSystem(new Thruster(2, 5, 0, 2, 3));
        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
	$this->addFrontSystem(new AmmoMissileRackS(4, 0, 0, 240, 60, $ammoMagazine, false));
	$this->addFrontSystem(new AmmoMissileRackS(4, 0, 0, 240, 60, $ammoMagazine, false));
		$this->addFrontSystem(new AmmoMissileRackL(4, 0, 0, 300, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new AmmoMissileRackL(4, 0, 0, 300, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new AmmoMissileRackR(4, 0, 0, 300, 60, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new TwinArray(4, 6, 2, 0, 180));
        
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 2, 2));
	$this->addAftSystem(new Hangar(4, 4));
        $this->addAftSystem(new ScatterGun(3, 8, 3, 180, 360));
        $this->addAftSystem(new ScatterGun(3, 8, 3, 180, 360));
		$this->addAftSystem(new AmmoMissileRackL(4, 0, 0, 300, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addAftSystem(new AmmoMissileRackL(4, 0, 0, 300, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 48));
        $this->addPrimarySystem(new Structure( 5, 48));
        $this->addAftSystem(new Structure( 4, 48));
        
        $this->hitChart = array(
        	0=> array(
        		9 => "Structure",
        		12 => "Thruster",
        		14 => "Scanner",
        		17 => "Engine",
        		19 => "Reactor",
        		20 => "C&C",	
        	),
        	1=> array(
        		6 => "Thruster",
        		8 => "Class-S Missile Rack",
        		9 => "Twin Array",
        		10 => "Class-R Missile Rack",
        		12 => "Class-L Missile Rack",
        		18 => "Structure",
        		20 => "Primary",        			
        	),
        	2=> array(
        		6 => "Thruster",
        		8 => "Scattergun",
				10 => "Class-L Missile Rack",
        		12 => "Hangar",
        		18 => "Structure",
        		20 => "Primary",        			
        	),
        );
    }
}
?>
