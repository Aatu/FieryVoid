<?php
class GromeGralac extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 900;
	$this->faction = "Grome";
        $this->phpclass = "GromeGralac";
        $this->imagePath = "img/ships/GromeGroth.png";
        $this->shipClass = "Gralac Heavy Carrier";
			$this->variantOf = "Groth Gunship";
			$this->occurence = "rare";
        $this->limited = 33;
        $this->shipSizeClass = 3;
		$this->canvasSize = 175; //img has 200px per side

	    $this->notes = 'Antiquated Sensors (cannot be boosted).';
	    $this->notes .= '<br>Targeting Arrays treated as a 1 point sensors.';

        $this->fighters = array("normal"=>54);

		$this->isd = 2260;
        
        $this->forwardDefense = 18;
        $this->sideDefense = 19;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 4;
        $this->rollcost = 99; //cannot roll
        $this->pivotcost = 4;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(4, 23, 0, 0));
        $this->addPrimarySystem(new CnC(4, 20, 0, 0));
        $this->addPrimarySystem(new AntiquatedScanner(3, 20, 6, 6));
		$targetingArray = new AntiquatedScanner(2, 6, 2, 1);
		$targetingArray->displayName = 'Targeting Array';
		$this->addPrimarySystem($targetingArray);
		$targetingArray = new AntiquatedScanner(2, 6, 2, 1);
		$targetingArray->displayName = 'Targeting Array';
		$this->addPrimarySystem($targetingArray);
        $this->addPrimarySystem(new Engine(4, 28, 0, 8, 4));
		$this->addPrimarySystem(new Hangar(2, 8));
		$this->addPrimarySystem(new JumpEngine(4, 20, 4, 36));
		
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
		$this->addFrontSystem(new Railgun(3, 9, 6, 300, 360));
		$this->addFrontSystem(new Railgun(3, 9, 6, 0, 60));
        $this->addFrontSystem(new FlakCannon(5, 4, 2, 240, 60));
        $this->addFrontSystem(new FlakCannon(5, 4, 2, 300, 120));
        $this->addFrontSystem(new ConnectionStrut(4));

        $this->addAftSystem(new Thruster(3, 16, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 16, 0, 4, 2));
		$this->addAftSystem(new Railgun(3, 9, 6, 180, 240));
		$this->addAftSystem(new Railgun(3, 9, 6, 120, 180));
        $this->addAftSystem(new ConnectionStrut(4));

        $this->addLeftSystem(new FlakCannon(2, 4, 2, 180, 360));
        $this->addLeftSystem(new FlakCannon(2, 4, 2, 180, 360));
        $this->addLeftSystem(new FlakCannon(2, 4, 2, 180, 360));
        $this->addLeftSystem(new FlakCannon(2, 4, 2, 180, 360));
        $this->addLeftSystem(new Thruster(3, 15, 0, 4, 3));
		$this->addLeftSystem(new Hangar(3, 12));
		$this->addLeftSystem(new Hangar(3, 12));
        $this->addLeftSystem(new ConnectionStrut(4));

        $this->addRightSystem(new FlakCannon(2, 4, 2, 0, 180));
        $this->addRightSystem(new FlakCannon(2, 4, 2, 0, 180));
        $this->addRightSystem(new FlakCannon(2, 4, 2, 0, 180));
        $this->addRightSystem(new FlakCannon(2, 4, 2, 0, 180));
        $this->addRightSystem(new Thruster(3, 15, 0, 4, 4));
		$this->addRightSystem(new Hangar(3, 12));
		$this->addRightSystem(new Hangar(3, 12));
        $this->addRightSystem(new ConnectionStrut(4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 94));
        $this->addAftSystem(new Structure(4, 90));
        $this->addLeftSystem(new Structure(4, 110));
        $this->addRightSystem(new Structure(4, 110));
        $this->addPrimarySystem(new Structure(4, 80));
		
		$this->hitChart = array(
			0=> array(
					6 => "Structure",
					8 => "Targeting Array",
					10 => "Jump Engine",
					13 => "Engine",
					16 => "Antiquated Scanner",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					5 => "Thruster",
					8 => "Railgun",
					11 => "Flak Cannon",
					15 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Railgun",
					15 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					7 => "Hangar",
					10 => "Flak Cannon",
					15 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					7 => "Hangar",
					10 => "Flak Cannon",
					15 => "Structure",
					18 => "Connection Strut",
					20 => "Primary",
			),
		);
    }
}

?>
