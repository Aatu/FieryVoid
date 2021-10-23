<?php
class GromeMorgatFull extends MediumShip{
/*Morgat with fully decked out railguns with special shells!*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
/*To account for the increased flexibility a points increase is needed as we cannont, yet,
purchase individual shells individually. To do this, I am assuming that each railgun is
equipped with 2 of the most expensive shells (heavies). Most shells are cheaper, but using
heavies ensures a realatively fair cost as a player is unlikely to use a standard shell 
on one of these "full" units. The final point total will be adjusted to be divisible by 5.
The costs before rounding the final value are: 
			Heavy railgun ~36 points
			Railgun ~24 points
			Light railgun ~12 points
		Estimated cost is 450 + (1x24) + (6x12) = 546 -> rounded to 545*/
        $this->pointCost = 545;
        $this->faction = "Grome";
        $this->phpclass = "GromeMorgatFull";
		$this->variantOf = "Morgat Heavy Frigate";
        $this->imagePath = "img/ships/GromeMorgat.png";
        $this->shipClass = "Morgat Heavy Frigate (full)";
        $this->canvasSize = 75;
	    $this->isd = 2218;
	    $this->unofficial = true;

	    $this->notes = 'Antiquated Sensors (cannot be boosted).';
	    $this->notes .= '<br>Targeting Array treated as a 1 point sensor.';
        
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.50;
        $this->turndelaycost = 0.50;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        $this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(3, 18, 0, 0));
        $this->addPrimarySystem(new CnC(3, 12, 0, 0));
        $this->addPrimarySystem(new AntiquatedScanner(3, 12, 4, 5));
		$targetingArray = new AntiquatedScanner(2, 6, 2, 1);
			$targetingArray->displayName = 'Targeting Array';
			$targetingArray->iconPath = "TargetingArray.png";
			$this->addPrimarySystem($targetingArray);
        $this->addPrimarySystem(new Engine(3, 16, 0, 6, 3));
        $this->addPrimarySystem(new Hangar(2, 1));
		$this->addPrimarySystem(new FlakCannon(2, 4, 2, 0, 360));
        $this->addPrimarySystem(new Thruster(2, 13, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(2, 13, 0, 3, 4));     
        $this->addPrimarySystem(new ConnectionStrut(3));
        
		$this->addFrontSystem(new GromeLgtRailgun(2, 6, 3, 300, 60));
		$this->addFrontSystem(new GromeLgtRailgun(2, 6, 3, 300, 60));
		$this->addFrontSystem(new GromeLgtRailgun(2, 6, 3, 300, 60));
		$this->addFrontSystem(new GromeLgtRailgun(2, 6, 3, 300, 60));
		$this->addFrontSystem(new GromeMedRailgun(2, 9, 6, 300, 60));
        $this->addFrontSystem(new Thruster(2, 10, 0, 3, 1));
	    
        $this->addAftSystem(new Thruster(2, 8, 0, 3, 2));    
        $this->addAftSystem(new Thruster(2, 8, 0, 3, 2));    
		$this->addAftSystem(new GromeLgtRailgun(2, 6, 3, 120, 240));
		$this->addAftSystem(new GromeLgtRailgun(2, 6, 3, 120, 240));
       
        $this->addPrimarySystem(new Structure(3, 85));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			4 => "Thruster",
			6 => "Connection Strut",
			7 => "Targeting Array",
			9 => "Flak Cannon",
			12 => "Engine",
			15 => "Scanner",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			4 => "Thruster",
			5 => "Railgun",
			9 => "Light Railgun",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Thruster",
			9 => "Light Railgun",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
