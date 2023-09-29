<?php
class ScoravarefittedAM extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 550;
		$this->faction = "Descari";
        $this->phpclass = "ScoravarefittedAM";
        $this->imagePath = "img/ships/DescariScorava.png";
        $this->shipClass = "Scorava Cruiser (Refit)";
        $this->shipSizeClass = 3;
        $this->fighters = array("medium"=>24, "normal"=>6);  
	    $this->isd = 2242;
	    $this->variantOf = "Scorava Cruiser";	//Listed as separate hull in Showdowns 6 but made a variant here for tidier fleet selection.        
        $this->occurence = "common";   
		
        $this->forwardDefense = 16;
        $this->sideDefense = 18;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 2;

        
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
		
        $this->addPrimarySystem(new Reactor(5, 25, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 18, 4, 6));
        $this->addPrimarySystem(new Engine(5, 20, 0, 12, 3));
		$this->addPrimarySystem(new Hangar(5, 26));
		$this->addPrimarySystem(new Hangar(2, 6));		
        
        $this->addFrontSystem(new LightPlasmaBolter(2, 0, 0, 300, 60));
        $this->addFrontSystem(new LightPlasmaBolter(2, 0, 0, 300, 60));       
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));        
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
			
        $this->addAftSystem(new LightPlasmaBolter(2, 0, 0, 120, 240));
        $this->addAftSystem(new LightPlasmaBolter(2, 0, 0, 120, 240));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270)); 
        $this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
		
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));	
		$this->addLeftSystem(new MediumPlasmaBolter(4, 0, 0, 300, 0));
        $this->addLeftSystem(new AmmoMissileRackS(4, 0, 0, 180, 60, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addLeftSystem(new Thruster(4, 15, 0, 4, 3));
              			  
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));	
		$this->addRightSystem(new MediumPlasmaBolter(4, 0, 0, 0, 60));
        $this->addRightSystem(new AmmoMissileRackS(4, 0, 0, 300, 180, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addRightSystem(new Thruster(4, 15, 0, 4, 4));
        
		
		//structures
        $this->addFrontSystem(new Structure(4, 44));
        $this->addAftSystem(new Structure(4, 48));
        $this->addLeftSystem(new Structure(4, 62));
        $this->addRightSystem(new Structure(4, 62));
        $this->addPrimarySystem(new Structure(5, 45));
		
		
		$this->hitChart = array(
			0=> array(
				9 => "Structure",
				11 => "Scanner",
				13 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				5 => "Thruster",
				8 => "Light Plasma Bolter",
				10 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				9 => "Thruster",
				11 => "Light Plasma Bolter",
				13 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				4 => "Thruster",
				6 => "Medium Plasma Bolter",
				7 => "Class-S Missile Rack", //missing on original SCS!
				9 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				4 => "Thruster",
				6 => "Medium Plasma Bolter",
				7 => "Class-S Missile Rack", //missing on original SCS!
				9 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
		);        
    }	
}



?>
