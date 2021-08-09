<?php
class FederationNXWarCruiser extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 400;
        $this->faction = "Custom Ships";
        $this->phpclass = "FederationNXWarCruiser";
        $this->imagePath = "img/ships/StarTrek/EnterpriseNX.png";
        $this->shipClass = "NX War Cruiser";
		$this->unofficial = true;
        $this->canvasSize = 80;
	    $this->isd = 2152;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 60;


		//BioDrive - first so javascript routines can easily find biothrusters they'll be looking for a lot!
		$bioDrive = new BioDrive(); //BioDrive just is, all parameters needed are calculated automatically
		
		$bioThruster = new BioThruster(2,6,2); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$bioThruster->displayName = 'Impulse Drive';
		$this->addAftSystem($bioThruster);
		
		$bioThruster = new BioThruster(2,8,3); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$bioThruster->displayName = 'Impulse Drive';
		$this->addAftSystem($bioThruster);
		
		$bioThruster = new BioThruster(2,8,3); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$bioThruster->displayName = 'Impulse Drive';
		$this->addAftSystem($bioThruster);
		
		$bioThruster = new BioThruster(2,6,2); //armour, health, output
		$bioDrive->addThruster($bioThruster);
		$bioThruster->displayName = 'Impulse Drive';
		$this->addAftSystem($bioThruster);
				
       		$this->addPrimarySystem($bioDrive);


         
        $this->addPrimarySystem(new Reactor(3, 10, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(2, 9, 2, 4));
        $this->addPrimarySystem(new Engine(3, 9, 0, 10, 2));
        $this->addPrimarySystem(new Hangar(3, 2));
		$grappler = new CustomIndustrialGrappler(2, 5, 0, 0, 360);
			$grappler->displayName = "Magnetic Grappler";
			$this->addPrimarySystem($grappler);

        
		$this->addFrontSystem(new TrekPhaseCannon(3, 6, 4, 240, 360));
		$this->addFrontSystem(new TrekPhaseCannon(3, 6, 4, 300, 60));
		$this->addFrontSystem(new TrekPhaseCannon(3, 6, 4, 360, 120));
		$this->addFrontSystem(new TrekPhotonicTorp(2, 4, 1, 270, 90));
		$this->addFrontSystem(new TrekPhotonicTorp(2, 4, 1, 270, 90));
	    
		$this->addAftSystem(new TrekWarpDrive(2, 18, 3, 13));
		$this->addAftSystem(new TrekWarpDrive(2, 18, 3, 13));
		$this->addAftSystem(new TrekPhaseCannon(2, 6, 4, 120, 240));
		$this->addAftSystem(new TrekPhotonicTorp(2, 4, 1, 120, 240));
		
		//technical thrusters - unlimited, like for MCVs		
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance  

        $this->addPrimarySystem(new Structure(3, 60));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			6 => "Magnetic Grappler",
			9 => "Scanner",
			12 => "Hangar",
			15 => "Engine",
			18 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			5 => "Phase Cannon",
			8 => "Photonic Torpedo",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Impulse Drive",
			7 => "Phase Cannon",
			8 => "Photonic Torpedo",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
