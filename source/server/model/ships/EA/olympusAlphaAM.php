<?php
class OlympusAlphaAM extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 500;
        $this->faction = "Earth Alliance";
        $this->phpclass = "OlympusAlphaAM";
        $this->imagePath = "img/ships/olympus.png";
        $this->shipClass = "Olympus Corvette (Alpha)";
        $this->variantOf = "Olympus Corvette (Delta)";
	    $this->isd = 2200;
                
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 1;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;
        

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(24); //pass magazine capacity - class-SO launchers hold 12 rounds per mount
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 24); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-L
        $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-L
        $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-L
        //no way this variant was around at the time of Minbari War! whatever survived Dilgar War must have been upgraded to Delta already; so Piercing missile is not available
        //$this->enhancementOptionsEnabled[] = 'AMMO_P';//add enhancement options for other missiles - Class-P
      
        $this->addPrimarySystem(new Reactor(4, 18, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 4, 5));
        $this->addPrimarySystem(new Engine(4, 11, 0, 6, 3));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 4, 4));
        
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 240, 0));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 240, 0));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 0, 120));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 0, 120));
        $this->addFrontSystem(new InterceptorMkI(2, 4, 1, 270, 90));
        $this->addFrontSystem(new RailGun(3, 9, 6, 0, 360));
        
        $this->addAftSystem(new AmmoMissileRackSO(3, 0, 0, 240, 360, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addAftSystem(new AmmoMissileRackSO(3, 0, 0, 0, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addAftSystem(new InterceptorMkI(2, 4, 1, 0, 360));
        $this->addAftSystem(new Thruster(2, 6, 0, 1, 2));
        $this->addAftSystem(new Thruster(3, 7, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 7, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 6, 0, 1, 2));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 48));
        $this->addAftSystem(new Structure( 4, 42));
        $this->addPrimarySystem(new Structure( 4, 50));
        
        $this->hitChart = array(
                0=> array(
                        8 => "Structure",
                        11 => "Thruster",
                        13 => "Scanner",
                        15 => "Engine",
                        16 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        3 => "Thruster",
                        5 => "Medium Plasma Cannon",
                        7 => "Railgun",
                        9 => "Interceptor MK I",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        8 => "Class-SO Missile Rack",
                        10 => "Railgun",
                        12 => "Interceptor MK I",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );        
    }

}

?>
