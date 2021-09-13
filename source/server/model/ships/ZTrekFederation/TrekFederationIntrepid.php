<?php
class TrekFederationIntrepid extends MediumShip{
	
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 250;
        $this->faction = "ZTrek Playtest Federation";
        $this->phpclass = "TrekFederationIntrepid";
        $this->imagePath = "img/ships/StarTrek/Intrepid.png";
        $this->shipClass = "Intrepid";

	$this->unofficial = true;
        $this->canvasSize = 100;
	$this->isd = 2150;

	$this->fighters = array("Shuttlecraft"=>2);
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;

        $this->gravitic = true;    
        $this->turncost = 0.5;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 60;

        $this->addPrimarySystem(new Reactor(3, 8, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 3, 3));
	$impulseDrive = new TrekImpulseDrive(3,15,0,1,3);
        $this->addPrimarySystem(new Hangar(3, 2));

		$polarizedhullplating = new AbsorbtionShield(2,4,3,1,270,90);  //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
		$polarizedhullplating->displayName = "Polarized Hull Plating";
		$this->addFrontSystem($polarizedhullplating);
		$this->addFrontSystem(new TrekPhaseCannon(3, 6, 4, 300, 60));
      		$this->addFrontSystem(new TrekSpatialTorp(2, 6, 1, 300, 60));
        	$this->addFrontSystem(new TrekSpatialTorp(2, 6, 1, 300, 60));
	    
		$warpNacelle = new TrekWarpDrive(3, 12, 3, 4); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
		$warpNacelle = new TrekWarpDrive(3, 12, 3, 4); //armor, structure, power usage, impulse output
		$impulseDrive->addThruster($warpNacelle);
		$this->addAftSystem($warpNacelle);
		$polarizedhullplating = new AbsorbtionShield(2,4,3,1,90,270);  //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
			$polarizedhullplating->displayName = "Polarized Hull Plating";
			$this->addAftSystem($polarizedhullplating);
        	$this->addAftSystem(new TrekSpatialTorp(2, 6, 1, 120, 240));

		//technical thrusters - unlimited, like for LCVs		
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addPrimarySystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance  

        $this->addPrimarySystem(new Structure(3, 45));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			6 => "Scanner",
			8 => "Hangar",
			14 => "Engine",
			17 => "Reactor",
			20 => "C&C",
		),

		1=> array(
		    	2 => "Polarized Hull Plating",
			5 => "Phase Cannon",
			9 => "Spatial Torpedo",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
		    	2 => "Polarized Hull Plating",
			9 => "Nacelle",
			11 => "Spatial Torpedo",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>