<?php
class TethysMissileAM extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 375;
		$this->faction = "Earth Alliance";
		$this->phpclass = "TethysMissileAM";
		$this->imagePath = "img/ships/tethys.png";
		$this->shipClass = "Tethys Missile Boat (Zeta)";
		$this->canvasSize = 100;
		$this->occurence = "rare";
	        $this->variantOf = 'Tethys Police Cutter (Kappa)';
	        $this->isd = 2212;

		$this->forwardDefense = 13;
		$this->sideDefense = 13;

		$this->turncost = 0.33;
		$this->turndelaycost = 0.5;
		$this->accelcost = 2;
		$this->rollcost = 1;
		$this->pivotcost = 1;
		$this->iniativebonus = 60;
		
		
        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(60); //pass magazine capacity - 20 rounds per launcher
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 60); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-L
        $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-L
        $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-L
        $this->enhancementOptionsEnabled[] = 'AMMO_P';//add enhancement options for other missiles - Class-P
		//frankly I don't see a glorified police ship getting full special missile selection, but... glorified police ships tend to be quite powerful in this game :)

		$this->addPrimarySystem(new Reactor(4, 13, 0, 3));
		$this->addPrimarySystem(new CnC(4, 9, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 10, 3, 5));
		$this->addPrimarySystem(new Engine(4, 11, 0, 8, 3));
		$this->addPrimarySystem(new Hangar(4, 2));
		$this->addPrimarySystem(new Thruster(3, 13, 0, 4, 3));
		$this->addPrimarySystem(new Thruster(3, 13, 0, 4, 4));
		
        $this->addFrontSystem(new Thruster(3, 7, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 7, 0, 3, 1));
        $this->addFrontSystem(new AmmoMissileRackS(3, 0, 0, 180, 0, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new AmmoMissileRackS(3, 0, 0, 0, 180, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 270, 90));
        $this->addFrontSystem(new InterceptorMkI(2, 4, 1, 270, 90));
		
        $this->addAftSystem(new Thruster(4, 8, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 4, 2));
        $this->addAftSystem(new AmmoMissileRackS(2, 0, 0, 90, 270, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 0, 180));
        $this->addAftSystem(new InterceptorMkI(2, 4, 1, 90, 270));
	
        $this->addPrimarySystem(new Structure( 4, 38));
		
		
		
		$this->hitChart = array(
                0=> array(
                        9 => "Thruster",
                        11 => "Scanner",
                        14 => "Engine",
                        16 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        6 => "Thruster",
                        8 => "Class-S Missile Rack",
                        9 => "Standard Particle Beam",
                        11 => "Interceptor I",
                        17 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        8 => "Thruster",
                        9 => "Class-S Missile Rack",
                        11 => "Standard Particle Beam",
                        12 => "Interceptor I",
                        17 => "Structure",
                        20 => "Primary",
                ),
        );
    }

}
?>
