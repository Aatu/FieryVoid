<?php
class OrestesBetaAM extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 500;
        $this->faction = "Earth Alliance (Early)";
		$this->phpclass = "OrestesBetaAM";
		$this->imagePath = "img/ships/orestes.png";
		$this->shipClass = "Orestes System Monitor (Beta)";
	        $this->variantOf = "Orestes System Monitor (Alpha)";
			$this->occurence = "common";
 		$this->unofficial = 'S'; //HRT design released after AoG demise
        $this->shipSizeClass = 3;
        $this->limited = 33;

		$this->fighters = array("normal"=>12);
	    
	    $this->isd = 2168;

		$this->forwardDefense = 16;
		$this->sideDefense = 16;

		$this->turncost = 1;
		$this->turndelaycost = 1;
		$this->accelcost = 5;
		$this->rollcost = 3;
		$this->pivotcost = 4;

		$this->iniativebonus = -10;

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(48); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 48); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
        $this->enhancementOptionsEnabled[] = 'AMMO_C';//add enhancement options for other missiles - Class-C
        $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-F        
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
        $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
		//I assume "Old" EA is Dilgar War era, at the latest - so no Minbari War-designed Piercing Missile, Starburst or Multiwarhead.

		$this->addPrimarySystem(new Reactor(4, 16, 0, 0));
		$this->addPrimarySystem(new CnC(5, 16, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 14, 3, 5));
		$this->addPrimarySystem(new Engine(4, 11, 0, 5, 4));
		$this->addPrimarySystem(new Hangar(4, 14));
		$this->addPrimarySystem(new LightParticleBeamShip(3, 2, 1, 0, 360));
		$this->addPrimarySystem(new LightParticleBeamShip(3, 2, 1, 0, 360));

		$this->addFrontSystem(new Thruster(3, 20, 0, 5, 1));
		$this->addFrontSystem(new AmmoMissileRackSO(3, 0, 0, 240, 60, $ammoMagazine, false));
		$this->addFrontSystem(new HeavyPlasma(3, 8, 5, 300, 60));
		$this->addFrontSystem(new InterceptorPrototype(2, 4, 1, 270, 90));
		$this->addFrontSystem(new HeavyPlasma(3, 8, 5, 300, 60));
		$this->addFrontSystem(new AmmoMissileRackSO(3, 0, 0, 300, 120, $ammoMagazine, false));

		$this->addAftSystem(new Thruster(4, 20, 0, 5, 2));
		$this->addAftSystem(new AmmoMissileRackSO(3, 0, 0, 120, 300, $ammoMagazine, false));
		$this->addAftSystem(new InterceptorPrototype(2, 4, 1, 90, 270));
		$this->addAftSystem(new AmmoMissileRackSO(3, 0, 0, 60, 240, $ammoMagazine, false));

		$this->addLeftSystem(new Thruster(3, 15, 0, 3, 3));
		$this->addLeftSystem(new MediumPlasma(3, 5, 3, 180, 360));
		$this->addLeftSystem(new MediumPlasma(3, 5, 3, 180, 360));

		$this->addRightSystem(new Thruster(3, 15, 0, 3, 4));
		$this->addRightSystem(new MediumPlasma(3, 5, 3, 0, 180));
		$this->addRightSystem(new MediumPlasma(3, 5, 3, 0, 180));
        
        $this->addFrontSystem(new Structure( 4, 42));
        $this->addAftSystem(new Structure( 4, 40));
        $this->addLeftSystem(new Structure( 4, 60));
        $this->addRightSystem(new Structure( 4, 60));
        $this->addPrimarySystem(new Structure( 5, 60));
		
		
		$this->hitChart = array(
                0=> array(
                        9 => "Structure",
                        11 => "Light Particle Beam",
                        13 => "Scanner",
                        15 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        6 => "Heavy Plasma Cannon",
                        9 => "Class-SO Missile Rack",
                        11 => "Interceptor Prototype",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        8 => "Class-SO Missile Rack",
                        10 => "Interceptor Prototype",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
                        9 => "Medium Plasma Cannon",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
                        9 => "Medium Plasma Cannon",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );

    }
}
?>
