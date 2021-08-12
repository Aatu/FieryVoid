<?php
class FederationConstitution extends HeavyCombatVessel{
	
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 450;
	$this->faction = "Custom Ships";
        $this->phpclass = "FederationConstitution";
        $this->imagePath = "img/ships/StarTrek/Constitution.png";
        $this->shipClass = "Federation Constitution Light Cruiser";

	$this->unofficial = true;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
 
        $this->gravitic = true;       
        $this->turncost = 0.66;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
	$this->iniativebonus = 6 *5; 
		

	    
		//BioDrive - first so javascript routines can easily find biothrusters they'll be looking for a lot!
		$bioDrive = new BioDrive(); //BioDrive just is, all parameters needed are calculated automatically
		
		$bioThruster = new BioThruster(3,8,2); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$bioThruster->displayName = 'Impulse Drive';
		$this->addAftSystem($bioThruster);
		
		$bioThruster = new BioThruster(3,8,2); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$bioThruster->displayName = 'Impulse Drive';
		$this->addAftSystem($bioThruster);
		
		$bioThruster = new BioThruster(3,8,2); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$bioThruster->displayName = 'Impulse Drive';
		$this->addAftSystem($bioThruster);
		
		$bioThruster = new BioThruster(3,8,2); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$bioThruster->displayName = 'Impulse Drive';
		$this->addAftSystem($bioThruster);
				
       		$this->addPrimarySystem($bioDrive);


	$this->addPrimarySystem(new CnC(4, 10, 0, 0));
        $this->addPrimarySystem(new Reactor(4, 12, 0, 2));
        $this->addPrimarySystem(new Scanner(4, 12, 4, 4));
        $this->addPrimarySystem(new Engine(4, 12, 0, 8, 3));
	$this->addPrimarySystem(new Hangar(3, 6, 3));
  
	//$this->addFrontSystem(new AbsorbtionShield(2,4,3,1,270,90) ); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc

	$this->addFrontSystem(new EWHeavyRocketLauncher(3, 6, 3, 270, 90));
        $this->addFrontSystem(new EWHeavyRocketLauncher(3, 6, 3, 270, 90));
	$this->addFrontSystem(new ParticleAccelerator(3, 6, 4, 180, 360));
	$this->addFrontSystem(new ParticleAccelerator(3, 6, 4, 270, 90));
	$this->addFrontSystem(new ParticleAccelerator(3, 6, 4, 270, 90));
	$this->addFrontSystem(new ParticleAccelerator(3, 6, 4, 0, 180));
	    
	//$this->addAftSystem(new AbsorbtionShield(2,4,3,1,90,270));


/*	$warpdrive = new JumpEngine(3, 20, 3, 13);
		$warpdrive->displayName = 'Warp Drive';
		$this->addAftSystem($warpdrive);
		$this->addAftSystem($warpdrive);
	$warpdrive = new JumpEngine(3, 20, 3, 13);
		$warpdrive->displayName = 'Warp Drive';
		$this->addAftSystem($warpdrive);
		$this->addAftSystem($warpdrive); */
//	$this->addAftSystem(new JumpEngine(3, 20, 3, 13));
//	$this->addAftSystem(new JumpEngine(3, 20, 3, 13));


	$this->addAftSystem(new TrekWarpDrive(3, 20, 3, 13));
	$this->addAftSystem(new TrekWarpDrive(3, 20, 3, 13));
		
		//technical thrusters - unlimited, like for MCVs		
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance  
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 36));
        $this->addAftSystem(new Structure( 3, 30));
        $this->addPrimarySystem(new Structure( 4, 32));
	    
	    
        $this->hitChart = array(
            0=> array(
                    8 => "2:BioThruster",
                    12 => "Scanner",
                    15 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Heavy Rocket Launcher",
                    9 => "Particle Accelerator",
		    //12 => "Absorbtion Shield",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    5 => "2:BioThruster",
		    9 => "Jump Engine",
		    //12 => "Absorbtion Shield",
                    18 => "Structure",
                    20 => "Primary",
            ),
       );
	    
	    
    }
}
?>