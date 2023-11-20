<?php
class GromeTrokanMargusFull extends BaseShip{
/*Trokan Margus with fully decked out railguns with special shells!*/    
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
		Estimated cost is 1200 + (5x36) + (4x24) = 1476 -> rounded to 1475*/        
	$this->pointCost = 1475; 
	$this->faction = "Grome Autocracy";
        $this->phpclass = "GromeTrokanMargusFull";
        $this->imagePath = "img/ships/GromeTrokan.png";
        $this->shipClass = "Trokan Margus Command Flagship (full)";
			$this->variantOf = 'Trokan Flagship';
			$this->occurence = "unique";
		$this->limited = 10;
        $this->shipSizeClass = 3;
		$this->canvasSize = 180; //img has 200px per side
	    $this->unofficial = true;

	    $this->notes = 'Antiquated Sensors (cannot be boosted).';
	    $this->notes .= '<br>Targeting Arrays treated as a 1 point sensors.';

        $this->fighters = array("normal"=>12);

		$this->isd = 2260;

		$this->enhancementOptionsDisabled[] = 'IMPR_REA';
		$this->enhancementOptionsDisabled[] = 'IMPR_ENG';
		$this->enhancementOptionsDisabled[] = 'IMPR_SENS';
		
//		$this->notes = "Engine fluctuations. Rolls for engine critical every turn with +5% penalty. Effect lasts one turn.";
//		$this->notes .= "<br>Power fluctuations. Rolls for reactor critical every turn with +5% penalty. Effect lasts one turn.";
//		$this->notes .= "<br>Sensor fluctuations. Rolls for sensor critical every turn with +5% penalty. Effect lasts one turn.";
//		$this->notes .= "<br>Communications problems. Rolls for C&C critical every turn with +5% penalty. Effect lasts one turn.";
        
        $this->forwardDefense = 18;
        $this->sideDefense = 19;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 4;
        $this->rollcost = 99; //cannot roll
        $this->pivotcost = 4;
        $this->iniativebonus = -15; //
        
		$reactor = new Reactor(5, 33, 0, 0);
			$reactor->markPowerFlux();
			$this->addPrimarySystem($reactor);
//        $this->addPrimarySystem(new Reactor(5, 33, 0, 0));  
		$cnc = new CnC(4, 42, 0, 0);
			$cnc->markCommsFlux();
			$this->addPrimarySystem($cnc);
//        $this->addPrimarySystem(new CnC(4, 30, 0, 0));
//        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
		$sensor = new AntiquatedScanner(4, 24, 6, 7);
			$sensor->markAntSensorFlux();
			$this->addPrimarySystem($sensor);
//        $this->addPrimarySystem(new AntiquatedScanner(4, 24, 6, 6)); //kept at 6 for sensor fluctuations
		$targetingArray = new AntiquatedScanner(2, 6, 2, 1);
			$targetingArray->displayName = 'Targeting Array';
			$targetingArray->iconPath = "TargetingArray.png";
			$this->addPrimarySystem($targetingArray);
		$targetingArray = new AntiquatedScanner(2, 6, 2, 1);
			$targetingArray->displayName = 'Targeting Array';
			$targetingArray->iconPath = "TargetingArray.png";
			$this->addPrimarySystem($targetingArray);
		$engine = new Engine(4, 36, 0, 12, 4);
			$engine->markEngineFlux();
			$this->addPrimarySystem($engine);
//        $this->addPrimarySystem(new Engine(4, 36, 0, 10, 4)); 
		$this->addPrimarySystem(new Hangar(3, 14));
		$this->addPrimarySystem(new JumpEngine(5, 20, 4, 36));
		
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
		$this->addFrontSystem(new GromeHvyRailgun(4, 12, 9, 330, 30));
		$this->addFrontSystem(new FlakCannon(2, 4, 2, 240, 60));
		$this->addFrontSystem(new FlakCannon(2, 4, 2, 240, 60));
		$this->addFrontSystem(new FlakCannon(2, 4, 2, 300, 120));
		$this->addFrontSystem(new FlakCannon(2, 4, 2, 300, 120));
        $this->addFrontSystem(new ConnectionStrut(4));

        $this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 20, 0, 6, 2));
		$this->addAftSystem(new FlakCannon(2, 4, 2, 120, 300));
		$this->addAftSystem(new FlakCannon(2, 4, 2, 120, 300));
		$this->addAftSystem(new FlakCannon(2, 4, 2, 60, 240));
		$this->addAftSystem(new FlakCannon(2, 4, 2, 60, 240));
        $this->addAftSystem(new ConnectionStrut(4));

        $this->addLeftSystem(new FlakCannon(2, 4, 2, 180, 360));
        $this->addLeftSystem(new FlakCannon(2, 4, 2, 180, 360));
        $this->addLeftSystem(new FlakCannon(2, 4, 2, 180, 360));
        $this->addLeftSystem(new FlakCannon(2, 4, 2, 180, 360));
		$this->addLeftSystem(new GromeHvyRailgun(3, 12, 9, 300, 360));
		$this->addLeftSystem(new GromeMedRailgun(3, 9, 6, 300, 360));
		$this->addLeftSystem(new GromeHvyRailgun(3, 12, 9, 180, 240));
		$this->addLeftSystem(new GromeMedRailgun(3, 9, 6, 180, 240));
        $this->addLeftSystem(new Thruster(3, 10, 0, 3, 3));
        $this->addLeftSystem(new Thruster(3, 10, 0, 3, 3));
        $this->addLeftSystem(new ConnectionStrut(4));

        $this->addRightSystem(new FlakCannon(2, 4, 2, 0, 180));
        $this->addRightSystem(new FlakCannon(2, 4, 2, 0, 180));
        $this->addRightSystem(new FlakCannon(2, 4, 2, 0, 180));
        $this->addRightSystem(new FlakCannon(2, 4, 2, 0, 180));
		$this->addRightSystem(new GromeHvyRailgun(3, 12, 9, 0, 60));
		$this->addRightSystem(new GromeMedRailgun(3, 9, 6, 0, 60));
		$this->addRightSystem(new GromeHvyRailgun(3, 12, 9, 120, 180));
		$this->addRightSystem(new GromeMedRailgun(3, 9, 6, 120, 180));
        $this->addRightSystem(new Thruster(3, 10, 0, 3, 4));
        $this->addRightSystem(new Thruster(3, 10, 0, 3, 4));
        $this->addRightSystem(new ConnectionStrut(4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 108));
        $this->addAftSystem(new Structure(4, 90));
        $this->addLeftSystem(new Structure(4, 110));
        $this->addRightSystem(new Structure(4, 110));
        $this->addPrimarySystem(new Structure(5, 96));
		
		$this->hitChart = array(
			0=> array(
					6 => "Structure",
					9 => "Targeting Array",
					11 => "Jump Engine",
					13 => "Engine",
					15 => "Scanner",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					5 => "Thruster",
					7 => "Heavy Railgun",
					12 => "Flak Cannon",
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
					7 => "Thruster",
					8 => "Railgun",
					10 => "Heavy Railgun",
					12 => "Flak Cannon",
					15 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
			4=> array(
					7 => "Thruster",
					8 => "Railgun",
					10 => "Heavy Railgun",
					12 => "Flak Cannon",
					15 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
		);
    }
}

?>
