<?php
class triadWraith extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 4300;
		$this->faction = "The Triad";
        $this->phpclass = "triadWraith";
        $this->shipClass = "Netrality: Wraith";
        $this->imagePath = "img/ships/triadWraith.png";
        $this->canvasSize = 200;
	    $this->isd = 'Primordial';
        $this->shipSizeClass = 3; 
		$this->factionAge = 4; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial

        $this->gravitic = true;
		$this->advancedArmor = true;  
        
        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 4;
		$this->iniativebonus = 3 *5;

		$this->notes .= 'Triad Capital Ship'; 

		$this->fighters = array("Triad Fighter"=>12);

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(10); //pass magazine capacity - 20 rounds per launcher, plus reload rack 80
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 10); //add full load of basic missiles
        
	    $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
	    $this->enhancementOptionsEnabled[] = 'AMMO_C';//add enhancement options for other missiles - Class-C
	    $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-F
	    $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H    
	    $this->enhancementOptionsEnabled[] = 'AMMO_I';//add enhancement options for other missiles - Class-I
	    $this->enhancementOptionsEnabled[] = 'AMMO_J';//add enhancement options for other missiles - Class-J	     
	    $this->enhancementOptionsEnabled[] = 'AMMO_K';//add enhancement options for other missiles - Class-K   
	    $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
	    $this->enhancementOptionsEnabled[] = 'AMMO_M';//add enhancement options for other missiles - Class-M	    
		$this->enhancementOptionsEnabled[] = 'AMMO_P';//add enhancement options for other missiles - Class-P    	    	    	    
	    $this->enhancementOptionsEnabled[] = 'AMMO_X';//add enhancement options for other missiles - Class-X		    	    	    	    
	    $this->enhancementOptionsEnabled[] = 'AMMO_S';//add enhancement options for other missiles - Class-S
//		$this->enhancementOptionsEnabled[] = 'AMMO_HM';//add enhancement options for other missiles - Class-HM

		/*Triad use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'TriadShip');

		$this->addPrimarySystem(new Reactor(8, 30, 0, 0));//armor, structure, power req, output
        $this->addPrimarySystem(new CnC(8, 16, 0, 0));
//		$scanner = new Scanner(8, 24, 0, 15);
//			$scanner->markAdvanced();
//			$this->addPrimarySystem($scanner);			
        $scanner = new ElintScanner(8, 24, 0, 14);  // Using this for now as Jealous ELINT not implemented
			$scanner->markMindrider();
			$this->addPrimarySystem($scanner);	        
		$this->addPrimarySystem(new Engine(8, 30, 0, 18, 3));
        $this->addPrimarySystem(new CoopStructureSelfRepair(8, 18, 18)); //armor, structure, output
		
        $this->addFrontSystem(new GraviticThruster(7, 13, 0, 5, 1));		
        $this->addFrontSystem(new GraviticThruster(7, 13, 0, 5, 1));		
        $this->addFrontSystem(new GraviticThruster(7, 13, 0, 5, 1));		
        $this->addFrontSystem(new NeutronBurst(6, 12, 4, 300, 60));	
        $this->addFrontSystem(new FuserArray(3, 24, 4, 300, 60));	
        $this->addFrontSystem(new NeutronBurst(6, 12, 4, 300, 60));	

		$this->addAftSystem(new GraviticThruster(7, 20, 0, 6, 2));
		$this->addAftSystem(new GraviticThruster(7, 20, 0, 6, 2));
		$this->addAftSystem(new GraviticThruster(7, 20, 0, 6, 2));
		$this->addAftSystem(new JumpEngine(8, 25, 6, 8));        
        $this->addAftSystem(new SelfRepair(8, 15, 6)); //armor, structure, output

        $this->addLeftSystem(new GraviticThruster(8, 25, 0, 8, 3)); 		
        $this->addLeftSystem(new AsteroidSalvo(8, 30, 10, 270, 90));	
        $this->addLeftSystem(new NeutronBurst(6, 12, 4, 240, 360));	
        $this->addLeftSystem(new AmmoMissileRackTriad(3, 6, 0, 240, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base

        $this->addRightSystem(new GraviticThruster(8, 25, 0, 8, 4)); 				
        $this->addRightSystem(new AsteroidSalvo(8, 30, 10, 270, 90));	
        $this->addRightSystem(new NeutronBurst(6, 12, 4, 0, 120));	
        $this->addRightSystem(new AmmoMissileRackTriad(3, 6, 0, 240, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 7, 45));
        $this->addAftSystem(new Structure( 8, 90));
        $this->addLeftSystem(new Structure( 7, 80));
        $this->addRightSystem(new Structure( 7, 80));
        $this->addPrimarySystem(new Structure( 8, 60 ));
	
		$this->hitChart = array(
			0=> array( //PRIMARY
				12 => "Structure",
				13 => "Cooperative Structure Self Repair",
				15 => "ELINT Scanner",                
				17 => "Engine",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array( //Fwd
				6 => "Thruster",
				9 => "Fuser Array", 
				11 => "Neutron Burst",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array( //Aft
				6 => "Thruster",
				8 => "Self Repair",
				10 => "Jump Engine",
				18 => "Structure",  				
				20 => "Primary",
			),
			3=> array( //Port
				4 => "Thruster",
				6 => "Triad Missile Rack", 
				9 => "Asteroid Salvo",
				10 => "Neutron Burst",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array( //Stbd
				4 => "Thruster",
				6 => "Triad Missile Rack", 
				9 => "Asteroid Salvo",
				10 => "Neutron Burst",
				18 => "Structure",
				20 => "Primary",
			),
		);
		
    }
}



?>
