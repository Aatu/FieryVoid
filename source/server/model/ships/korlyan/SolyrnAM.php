<?php
class SolyrnAM extends HeavyCombatVessel{
  //Olympus equipped with actual working Ammunition Magazine
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 650;
        $this->faction = "Kor-Lyan";
        $this->phpclass = "SolyrnAM";
        $this->imagePath = "img/ships/korlyan_solyrn.png";
        $this->shipClass = "Solyrn Missile Destroyer";
        $this->limited = 10;
	    $this->isd = 2237;

		$this->canvasSize = 130; 

	    $this->notes = 'Atmospheric Capable.';
        $this->fighters = array("assault shuttles"=>2);
        
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 0.75;
        $this->turndelaycost = 0.75;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 30;
        
      
	//ammo magazine itself (AND its missile options)
	$ammoMagazine = new AmmoMagazine(120); //pass magazine capacity 
	    $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
	    $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 120); //add full load of basic missiles
	    $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
	    $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-L
	    $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-L
	    $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-L
	    $this->enhancementOptionsEnabled[] = 'AMMO_P';//add enhancement options for other missiles - Class-P
	    $this->enhancementOptionsEnabled[] = 'AMMO_S';//add enhancement options for other missiles - Class-S
	    $this->enhancementOptionsEnabled[] = 'AMMO_K';//add enhancement options for other missiles - Class-K	    
         
        $this->addPrimarySystem(new Reactor(4, 11, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 5, 6));
        $this->addPrimarySystem(new Engine(4, 15, 0, 9, 2));
        $this->addPrimarySystem(new ReloadRack(4, 9));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 4, 4));        
        
        
        $this->addFrontSystem(new Thruster(4, 18, 0, 6, 1));
        $this->addFrontSystem(new MultiDefenseLauncher(2, 'D', 240, 60, false));
        $this->addFrontSystem(new AmmoMissileRackR(3, 0, 0, 240, 60, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new AmmoMissileRackL(3, 0, 0, 240, 60, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new AmmoMissileRackL(3, 0, 0, 300, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new AmmoMissileRackR(3, 0, 0, 300, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new MultiDefenseLauncher(2, 'D', 300, 120, false));
		
        $this->addAftSystem(new MultiDefenseLauncher(3, 'D', 120, 300, false));
        $this->addAftSystem(new AmmoMissileRackL(3, 0, 0, 120, 300, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addAftSystem(new AmmoMissileRackL(3, 0, 0, 60, 240, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addAftSystem(new MultiDefenseLauncher(3, 'D', 60, 240, false));
        $this->addAftSystem(new Thruster(4, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 3, 2));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 48));
        $this->addAftSystem(new Structure( 4, 48));
        $this->addPrimarySystem(new Structure( 4, 48));
        
        
        $this->hitChart = array(
                0=> array(
                        7 => "Structure",
                        9 => "Thruster",
						11 => "Reload Rack",
                        13 => "Scanner",
                        15 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        6 => "Class-L Missile Rack",
                        8 => "Class-R Missile Rack",
                        10 => "Class-D Missile Rack",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        8 => "Class-L Missile Rack",
                        10 => "Class-D Missile Rack",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );
    }

}



?>
