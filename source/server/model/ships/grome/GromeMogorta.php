<?php
class GromeMogorta extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 650;
	$this->faction = "Grome";
        $this->phpclass = "GromeMogorta";
        $this->imagePath = "img/ships/GromeMogorta.png";
        $this->shipClass = "Mogorta Warship";
        $this->shipSizeClass = 3;
		$this->canvasSize = 165; //img has 200px per side

	    $this->notes = 'Antiquated Sensors (cannot be boosted).';
	    $this->notes .= '<br>Targeting Arrays treated as a 1 point sensors.';

        $this->fighters = array("normal"=>6);

		$this->isd = 2214;
        
        $this->forwardDefense = 16;
        $this->sideDefense = 17;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 4;
        $this->rollcost = 99; //cannot roll
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(4, 20, 0, 0));
        $this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new AntiquatedScanner(3, 16, 5, 6));
		$targetingArray = new AntiquatedScanner(2, 6, 2, 1);
			$targetingArray->displayName = 'Targeting Array';
			$targetingArray->iconPath = "TargetingArray.png";
			$this->addPrimarySystem($targetingArray);
		$targetingArray = new AntiquatedScanner(2, 6, 2, 1);
			$targetingArray->displayName = 'Targeting Array';
			$targetingArray->iconPath = "TargetingArray.png";
			$this->addPrimarySystem($targetingArray);
        $this->addPrimarySystem(new Engine(4, 24, 0, 6, 4));
		$this->addPrimarySystem(new Hangar(2, 8));
		$this->addPrimarySystem(new JumpEngine(4, 20, 4, 36));
		
        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
		$this->addFrontSystem(new Railgun(3, 9, 6, 300, 360));
		$this->addFrontSystem(new Railgun(3, 9, 6, 300, 360));
		$this->addFrontSystem(new Railgun(3, 9, 6, 0, 60));
		$this->addFrontSystem(new Railgun(3, 9, 6, 0, 60));
        $this->addFrontSystem(new ConnectionStrut(4));

        $this->addAftSystem(new Thruster(3, 16, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 16, 0, 3, 2));
		$this->addAftSystem(new Railgun(3, 9, 6, 180, 240));
		$this->addAftSystem(new Railgun(3, 9, 6, 120, 180));
        $this->addAftSystem(new ConnectionStrut(3));

        $this->addLeftSystem(new FlakCannon(2, 4, 2, 180, 360));
        $this->addLeftSystem(new FlakCannon(2, 4, 2, 180, 360));
        $this->addLeftSystem(new FlakCannon(2, 4, 2, 180, 360));
        $this->addLeftSystem(new Thruster(3, 15, 0, 3, 3));
        $this->addLeftSystem(new ConnectionStrut(3));

        $this->addRightSystem(new FlakCannon(2, 4, 2, 0, 180));
        $this->addRightSystem(new FlakCannon(2, 4, 2, 0, 180));
        $this->addRightSystem(new FlakCannon(2, 4, 2, 0, 180));
        $this->addRightSystem(new Thruster(3, 15, 0, 3, 4));
        $this->addRightSystem(new ConnectionStrut(3));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 84));
        $this->addAftSystem(new Structure(3, 80));
        $this->addLeftSystem(new Structure(3, 100));
        $this->addRightSystem(new Structure(3, 100));
        $this->addPrimarySystem(new Structure(4, 75));
		
		$this->hitChart = array(
			0=> array(
					7 => "Structure",
					9 => "Targeting Array",
					11 => "Jump Engine",
					13 => "Engine",
					16 => "Scanner",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					8 => "Railgun",
					15 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					10 => "Railgun",
					15 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
			3=> array(
					6 => "Thruster",
					10 => "Flak Cannon",
					15 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
			4=> array(
					6 => "Thruster",
					10 => "Flak Cannon",
					15 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
		);
    }
}

?>
