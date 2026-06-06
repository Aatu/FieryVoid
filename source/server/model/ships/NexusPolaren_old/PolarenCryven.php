<?php
class PolarenCryven extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 260;
        $this->faction = "Nexus Polaren Confederacy (early)";
        $this->phpclass = "PolarenCryven";
        $this->imagePath = "img/ships/Nexus/polarenFyron.png";
        $this->shipClass = "Cryven Assault Frigate";
			$this->variantOf = "Fyron Escort Frigate";
			$this->occurence = "uncommon";
		$this->unofficial = true;
        $this->canvasSize = 110;
        $this->agile = true;
	    $this->isd = 2009;

	    $this->notes = 'Atmospheric capable';
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        
		$this->agile = true;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 60;

   		$this->enhancementOptionsEnabled[] = 'ELT_MRN'; //To enable Elite Marines enhancement
		$this->enhancementOptionsEnabled[] = 'EXT_MRN'; //To enable extra Marines enhancement
         
        $this->addPrimarySystem(new Reactor(3, 12, 0, 3));
        $this->addPrimarySystem(new CnC(3, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 9, 3, 6));
        $this->addPrimarySystem(new Engine(3, 12, 0, 10, 3));
        $this->addPrimarySystem(new Thruster(2, 9, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(2, 9, 0, 5, 4));        
		$this->addPrimarySystem(new NexusSandCaster(1, 4, 2, 0, 360));
        
		$this->addFrontSystem(new GrapplingClaw(5, 0, 0, 300, 60, 6, false));
		$this->addFrontSystem(new LtBlastCannon(2, 4, 1, 240, 120));
		$this->addFrontSystem(new GrapplingClaw(5, 0, 0, 300, 60, 6, false));
        $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));
		$this->addFrontSystem(new Bulkhead(0, 2));
	    
		$this->addAftSystem(new Maser(2, 6, 3, 120, 360));
		$this->addAftSystem(new Maser(2, 6, 3, 0, 240));
        $this->addAftSystem(new Thruster(2, 9, 0, 5, 2));    
        $this->addAftSystem(new Thruster(2, 9, 0, 5, 2));    
		$this->addAftSystem(new Hangar(1, 1, 1));
		$this->addAftSystem(new Bulkhead(0, 2));
        
        $this->addPrimarySystem(new Structure(4, 33));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			10 => "Thruster",
			11 => "Sand Caster",
			14 => "Scanner",
			17 => "Engine",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			5 => "Thruster",
			6 => "Light Blast Cannon",
			9 => "Grappling Claw",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Thruster",
			8 => "Maser",
			9 => "Hangar",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
