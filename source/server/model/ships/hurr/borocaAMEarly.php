<?php
class borocaAMEarly extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 494;
        $this->faction = "Hurr Republic";
        $this->phpclass = "borocaAMEarly";
        $this->imagePath = "img/ships/hurrBoroca.png";
        $this->shipClass = "Boroca Gunship (Early)";
        $this->shipSizeClass = 3;
        
        $this->occurence = "common";
        $this->variantOf = 'Boroca Gunship';        

        $this->isd = 2225;
        
        $this->forwardDefense = 16;
        $this->sideDefense = 17;
        
        $this->turncost = 1.5;
        $this->turndelaycost = 1.5;
        $this->accelcost = 6;
        $this->rollcost = 4;
        $this->pivotcost = 4;
         
		 
        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(24); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 24); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
        $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-F
        $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
        //$this->enhancementOptionsEnabled[] = 'AMMO_P';//add enhancement options for other missiles - Class-P
		//Hurr developed their missiles from Dilgar tech - they have L,H,F and A missiles (even if only after Dilgar War)
		//they developed P missiles as well (just before Show era), but they remain very rare (tabletop limit of 1 per ship (2 per dedicated missile ship)). I opted to skip these missiles instead. 
		
		
        $this->addPrimarySystem(new Reactor(4, 20, 0, 0));
        $this->addPrimarySystem(new CnC(5, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 4, 5));
        $this->addPrimarySystem(new Hangar(2, 2));
   
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
	$this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new HeavyPlasma(4, 8, 5, 300, 0));
        $this->addFrontSystem(new HeavyPlasma(4, 8, 5, 0, 60));
		$this->addFrontSystem(new AmmoMissileRackSO(3, 0, 0, 300, 60, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new AmmoMissileRackSO(3, 0, 0, 300, 60, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        
	$this->addAftSystem(new Thruster(3, 16, 0, 4, 2));
	$this->addAftSystem(new Thruster(3, 16, 0, 4, 2));
	$this->addAftSystem(new Engine(4, 14, 0, 4, 5));
	$this->addAftSystem(new Engine(4, 14, 0, 4, 5));
       	$this->addAftSystem(new JumpEngine(4, 20, 4, 36));
                
        $this->addLeftSystem(new Thruster(3, 12, 0, 4, 3));
	$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 270, 90)); 
	$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 270, 90)); 
	$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 90, 270)); 
	$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 90, 270));  

        $this->addRightSystem(new Thruster(3, 12, 0, 4, 4));
	$this->addRightSystem(new StdParticleBeam(2, 4, 1, 270, 90)); 
	$this->addRightSystem(new StdParticleBeam(2, 4, 1, 270, 90)); 
	$this->addRightSystem(new StdParticleBeam(2, 4, 1, 90, 270)); 
	$this->addRightSystem(new StdParticleBeam(2, 4, 1, 90, 270)); 
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 42));
        $this->addAftSystem(new Structure( 5, 50));
        $this->addLeftSystem(new Structure( 4, 55));
        $this->addRightSystem(new Structure( 4, 55));
        $this->addPrimarySystem(new Structure( 5, 55));
        
        $this->hitChart = array(
        		0=> array(
        				12 => "Structure",
        				15 => "Scanner",
        				16 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				4 => "Thruster",
                                	8 => "Heavy Plasma Cannon",
                        		11 => "Class-S Missile Rack",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
                        		8 => "Jump Engine",
                        		13 => "Engine",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				6 => "Thruster",
                        		10 => "Standard Particle Beam",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				6 => "Thruster",
                        		10 => "Standard Particle Beam",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}


?>
