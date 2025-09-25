<?php
class HermesOldFastAlpha extends HeavyCombatVessel{
	//Richard Bax design - its presence explains A LOT of strange things about Hermes Priority Transport!
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 340;
        $this->faction = "Earth Alliance (Early)";
        $this->phpclass = "HermesOldFastAlpha";
        $this->imagePath = "img/ships/hermes.png";
        $this->shipClass = "Hermes Fast Transport (Alpha)";
 		$this->unofficial = 'S'; //HRT design released after AoG demise
        $this->isd = 2168;
	    $this->isCombatUnit = false; //not a combat unit, it will never be present in a regular battlegroup
        
        $this->fighters = array("heavy" => 6);
        
        $this->forwardDefense = 14;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        $this->iniativebonus = 6 *5;
        
         
        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(24); //pass magazine capacity - 12 rounds per launcher
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 24); //add full load of basic missiles		
        //$this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
        //$this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-L
        //$this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-L
        //$this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-L
        //$this->enhancementOptionsEnabled[] = 'AMMO_P';//add enhancement options for other missiles - Class-P		
		//Fast Transport is nice and well armed, but not really top priority (might be civilian, too) - and it's old... so no special missiles
		
        $this->addPrimarySystem(new Reactor(3, 12, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 4, 4));
        $this->addPrimarySystem(new Engine(4, 13, 0, 8, 2));
        $this->addPrimarySystem(new Hangar(3, 8, 6));
		$this->addPrimarySystem(new CargoBay(3, 30));
        $this->addPrimarySystem(new Thruster(3, 11, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(3, 11, 0, 3, 4));    
        $this->addPrimarySystem(new AmmoMissileRackSO(3, 0, 0, 0, 360, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addPrimarySystem(new AmmoMissileRackSO(3, 0, 0, 0, 360, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        
        $this->addFrontSystem(new InterceptorPrototype(2, 4, 1, 270, 90));
        $this->addFrontSystem(new Thruster(3, 7, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 7, 0, 2, 1));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 360));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 0, 120));

        
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));
		$this->addAftSystem(new CargoBay(3, 30));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        $this->addAftSystem(new InterceptorPrototype(2, 4, 1, 90, 270));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 36));
        $this->addAftSystem(new Structure( 3, 36));
        $this->addPrimarySystem(new Structure( 4, 41));
        
        $this->hitChart = array(
                0=> array(
                        6 => "Structure",
                        8 => "Cargo Bay",
                        10 => "Thruster",
                        12 => "Class-SO Missile Rack",
                        14 => "Scanner",
                        16 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        6 => "Light Particle Beam",
                        8 => "Interceptor I",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        8 => "Light Particle Beam",
                        10 => "Interceptor I",
                        12 => "Cargo Bay",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );
        
    }

}



?>
