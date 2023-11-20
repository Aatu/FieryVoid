<?php
class fwellgonAM extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 700;  //Original price (800) assumes ship gives +1 comand bonus. No command bonus here, price reduced.
        $this->faction = "Llort";
        $this->phpclass = "fwellgonAM";
        $this->imagePath = "img/ships/LlortGovall.png";
        $this->shipClass = "Fwellgon Raiding Scout";
        $this->limited = 33; 
 
        $this->isd = 2227;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;
        
        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(40); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 40); //add full load of basic missiles
	    $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
	    $this->enhancementOptionsEnabled[] = 'AMMO_C';//add enhancement options for other missiles - Class-C
	    $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-F
	    $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
	    $this->enhancementOptionsEnabled[] = 'AMMO_K';//add enhancement options for other missiles - Class-K   
	    $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
	    $this->enhancementOptionsEnabled[] = 'AMMO_M';//add enhancement options for other missiles - Class-M	    
		$this->enhancementOptionsEnabled[] = 'AMMO_P';//add enhancement options for other missiles - Class-P
	    $this->enhancementOptionsEnabled[] = 'AMMO_X';//add enhancement options for other missiles - Class-X		
		
        $this->addPrimarySystem(new Reactor(4, 15, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new ElintScanner(4, 12, 4, 6));
	$this->addPrimarySystem(new Engine(4, 12, 0, 8, 2));
        $this->addPrimarySystem(new Thruster(3, 8, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 5, 4));

        $this->addFrontSystem(new Thruster(2, 5, 0, 2, 3));
        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
	$this->addFrontSystem(new LightLaser(3, 4, 3, 240, 60));
        $this->addFrontSystem(new LightParticleCannon(4, 6, 5, 300, 60));
        $this->addFrontSystem(new ElintScanner(3, 6, 2, 2));
        $this->addFrontSystem(new ElintScanner(3, 6, 2, 2));
        $this->addFrontSystem(new ScatterGun(4, 8, 3, 300, 120));

        
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 2, 2));
	$this->addAftSystem(new Hangar(3, 3));
        $this->addAftSystem(new ScatterGun(3, 8, 3, 180, 360));
		$this->addAftSystem(new AmmoMissileRackS(4, 0, 0, 300, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addAftSystem(new AmmoMissileRackS(4, 0, 0, 300, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addAftSystem(new JumpEngine(4, 11, 4, 36));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 36));
        $this->addPrimarySystem(new Structure( 5, 42));
        $this->addAftSystem(new Structure( 4, 36));
        
        $this->hitChart = array(
        	0=> array(
        		10 => "Structure",
        		12 => "Thruster",
        		14 => "ELINT Scanner",
        		17 => "Engine",
        		19 => "Reactor",
        		20 => "C&C",	
        	),
        	1=> array(
        		6 => "Thruster",
        		8 => "Scattergun",
        		9 => "Light Laser",
        		10 => "Light Particle Cannon",
        		12 => "ELINT Scanner",
        		18 => "Structure",
        		20 => "Primary",        			
        	),
        	2=> array(
        		6 => "Thruster",
        		7 => "Scattergun",
			9 => "Class-S Missile Rack",
        		10 => "Hangar",
        		12 => "Jump Engine",
        		18 => "Structure",
        		20 => "Primary",        			
        	),
        );
    }
}
?>
