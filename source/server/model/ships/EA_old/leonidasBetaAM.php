<?php
class LeonidasBetaAM extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 130;
        $this->faction = "Earth Alliance (Early)";
        $this->phpclass = "LeonidasBetaAM";
        $this->imagePath = "img/ships/hector.png";
        $this->shipClass = 'Leonidas Satellite (Beta)';
			$this->variantOf = "Leonidas Satellite (Alpha)";
			$this->occurence = "common";
 		$this->unofficial = 'S'; //HRT design released after AoG demise

	    $this->isd = 2168;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(24); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 24); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
        $this->enhancementOptionsEnabled[] = 'AMMO_C';//add enhancement options for other missiles - Class-C
        $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-F        
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
        $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
		//I assume "Old" EA is Dilgar War era, at the latest - so no Minbari War-designed Piercing Missile, Starburst or Multiwarhead.
		
		
        $this->addFrontSystem(new AmmoMissileRackSO(3, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new AmmoMissileRackSO(3, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 0, 360));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        //$this->addPrimarySystem(new InterceptorMkI(2, 4, 1, 0, 360));

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));
        $this->addPrimarySystem(new Reactor(4, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 14, 2, 3));   

        $this->addAftSystem(new Thruster(2, 6, 0, 0, 2));
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(3, 30));
		
		$this->hitChart = array(
			0=> array(
				9 => "Structure",
				11 => "2:Thruster",
				14 => "1:Class-SO Missile Rack",
				17 => "1:Light Particle Beam",
				19 => "Scanner",
				20 => "Reactor",
			),
			1=> array(
				9 => "Structure",
				11 => "2:Thruster",
				14 => "1:Class-SO Missile Rack",
				17 => "1:Light Particle Beam",
				19 => "0:Scanner",
				20 => "0:Reactor",
			),
			2=> array(
				9 => "Structure",
				11 => "2:Thruster",
				14 => "1:Class-SO Missile Rack",
				17 => "1:Light Particle Beam",
				19 => "0:Scanner",
				20 => "0:Reactor",
			),
        );
    }
}
?>
