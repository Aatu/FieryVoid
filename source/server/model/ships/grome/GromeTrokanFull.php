<?php
class GromeTrokanFull extends BaseShip{
/*Trokan with fully decked out railguns with special shells!*/    
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
		Estimated cost is 900 + (1x36) + (8x24) = 1128 -> rounded to 1100*/        
	$this->pointCost = 1100;
	$this->faction = "Grome";
        $this->phpclass = "GromeTrokanFull";
		$this->variantOf = "Trokan Flagship";
        $this->imagePath = "img/ships/GromeTrokan.png";
        $this->shipClass = "Trokan Flagship (full)";
			$this->limited = 10;
        $this->shipSizeClass = 3;
		$this->canvasSize = 180; //img has 200px per side
	    $this->unofficial = true;

	    $this->notes = 'Antiquated Sensors (cannot be boosted).';
	    $this->notes .= '<br>Targeting Arrays treated as a 1 point sensors.';

        $this->fighters = array("normal"=>12);

		$this->isd = 2238;
        
        $this->forwardDefense = 18;
        $this->sideDefense = 19;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 4;
        $this->rollcost = 99; //cannot roll
        $this->pivotcost = 4;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(4, 25, 0, 0));
        $this->addPrimarySystem(new CnC(4, 30, 0, 0));
        $this->addPrimarySystem(new AntiquatedScanner(4, 20, 6, 6));
		$targetingArray = new AntiquatedScanner(2, 6, 2, 1);
			$targetingArray->displayName = 'Targeting Array';
			$targetingArray->iconPath = "TargetingArray.png";
			$this->addPrimarySystem($targetingArray);
		$targetingArray = new AntiquatedScanner(2, 6, 2, 1);
			$targetingArray->displayName = 'Targeting Array';
			$targetingArray->iconPath = "TargetingArray.png";
			$this->addPrimarySystem($targetingArray);
        $this->addPrimarySystem(new Engine(4, 32, 0, 10, 4));
		$this->addPrimarySystem(new Hangar(3, 14));
		$this->addPrimarySystem(new JumpEngine(4, 20, 4, 36));
		
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
		$this->addFrontSystem(new GromeHvyRailgun(4, 12, 9, 330, 30));
		$this->addFrontSystem(new FlakCannon(2, 4, 2, 240, 60));
		$this->addFrontSystem(new FlakCannon(2, 4, 2, 300, 120));
        $this->addFrontSystem(new ConnectionStrut(4));

        $this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 16, 0, 4, 2));
		$this->addAftSystem(new FlakCannon(2, 4, 2, 120, 300));
		$this->addAftSystem(new FlakCannon(2, 4, 2, 60, 240));
        $this->addAftSystem(new ConnectionStrut(4));

        $this->addLeftSystem(new FlakCannon(2, 4, 2, 180, 360));
        $this->addLeftSystem(new FlakCannon(2, 4, 2, 180, 360));
        $this->addLeftSystem(new FlakCannon(2, 4, 2, 180, 360));
		$this->addLeftSystem(new GromeMedRailgun(3, 9, 6, 300, 360));
		$this->addLeftSystem(new GromeMedRailgun(3, 9, 6, 300, 360));
		$this->addLeftSystem(new GromeMedRailgun(3, 9, 6, 180, 240));
		$this->addLeftSystem(new GromeMedRailgun(3, 9, 6, 180, 240));
        $this->addLeftSystem(new Thruster(3, 10, 0, 3, 3));
        $this->addLeftSystem(new Thruster(3, 10, 0, 3, 3));
        $this->addLeftSystem(new ConnectionStrut(4));

        $this->addRightSystem(new FlakCannon(2, 4, 2, 0, 180));
        $this->addRightSystem(new FlakCannon(2, 4, 2, 0, 180));
        $this->addRightSystem(new FlakCannon(2, 4, 2, 0, 180));
		$this->addRightSystem(new GromeMedRailgun(3, 9, 6, 0, 60));
		$this->addRightSystem(new GromeMedRailgun(3, 9, 6, 0, 60));
		$this->addRightSystem(new GromeMedRailgun(3, 9, 6, 120, 180));
		$this->addRightSystem(new GromeMedRailgun(3, 9, 6, 120, 180));
        $this->addRightSystem(new Thruster(3, 10, 0, 3, 4));
        $this->addRightSystem(new Thruster(3, 10, 0, 3, 4));
        $this->addRightSystem(new ConnectionStrut(4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 108));
        $this->addAftSystem(new Structure(4, 90));
        $this->addLeftSystem(new Structure(4, 110));
        $this->addRightSystem(new Structure(4, 110));
        $this->addPrimarySystem(new Structure(4, 88));
		
		$this->hitChart = array(
			0=> array(
					7 => "Structure",
					9 => "Targeting Array",
					11 => "Jump Engine",
					13 => "Engine",
					15 => "Antiquated Scanner",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					5 => "Thruster",
					7 => "Heavy Railgun",
					11 => "Flak Cannon",
					15 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					10 => "Flak Cannon",
					15 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
			3=> array(
					6 => "Thruster",
					9 => "Railgun",
					12 => "Flak Cannon",
					15 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
			4=> array(
					6 => "Thruster",
					9 => "Railgun",
					12 => "Flak Cannon",
					15 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
		);
    }
}

?>
