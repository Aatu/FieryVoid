<?php
class RulnatoAM extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 485;
        $this->faction = "Descari";
        $this->phpclass = "RulnatoAM";
        $this->imagePath = "img/ships/DescariRulnata.png";
        $this->shipClass = "Rulnato Jump Scout";
	    $this->isd = 2245;
        $this->limited = 33;
        
		$this->unofficial = true;        	    
        $this->variantOf = "Rulnata Scout";
		$this->occurence = "rare";        
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 1;
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
		
        $this->addPrimarySystem(new Reactor(5, 20, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new ElintScanner(5, 18, 9, 4));
        $this->addPrimarySystem(new Engine(5, 16, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(5, 2));
		$this->addPrimarySystem(new JumpEngine(4, 16, 5, 32));        
        $this->addPrimarySystem(new Thruster(3, 13, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 4, 4));
        $this->addPrimarySystem(new AmmoMissileRackS(3, 0, 0, 180, 60, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $
        $this->addPrimarySystem(new AmmoMissileRackS(3, 0, 0, 300, 180, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        
        
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new ElintScanner(4, 8, 4, 2));
        $this->addFrontSystem(new ElintScanner(4, 8, 4, 2));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 60));        
		
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 0, 180)); 
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));       
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 36));
        $this->addAftSystem(new Structure( 4, 36));
        $this->addPrimarySystem(new Structure( 5, 48));
		
			
		
		$this->hitChart = array(
			0=> array(
				6 => "Structure",
				9 => "Thruster",
				10 => "Jump Engine",
				12 => "Class-S Missile Rack",
				14 => "Scanner",
				16 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				4 => "Thruster",
				8 => "Scanner",
				10 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				6 => "Thruster",
				10 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
		); 
    }
}



?>