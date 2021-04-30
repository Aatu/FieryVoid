<?php
class GromeTelgar extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 400;
        $this->faction = "Grome";
        $this->phpclass = "GromeTelgar";
        $this->imagePath = "img/ships/GromeTelgar.png";
        $this->shipClass = "Telgar Defense Frigate";
        $this->canvasSize = 75;
	    $this->isd = 2218;

	    $this->notes = 'Antiquated Sensors (cannot be boosted).';
	    $this->notes .= '<br>Targeting Array treated as a 1 point sensor.';
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 0.50;
        $this->turndelaycost = 0.50;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        $this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(3, 15, 0, 0));
        $this->addPrimarySystem(new CnC(3, 12, 0, 0));
        $this->addPrimarySystem(new AntiquatedScanner(3, 12, 4, 4));
		$targetingArray = new AntiquatedScanner(2, 6, 2, 1);
			$targetingArray->displayName = 'Targeting Array';
			$targetingArray->iconPath = "TargetingArray.png";
			$this->addPrimarySystem($targetingArray);
        $this->addPrimarySystem(new Engine(3, 16, 0, 6, 3));
        $this->addPrimarySystem(new Hangar(1, 1));
        $this->addPrimarySystem(new Thruster(2, 13, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(2, 13, 0, 3, 4));     
        $this->addPrimarySystem(new ConnectionStrut(3));
        
		$this->addFrontSystem(new LightRailGun(2, 6, 3, 300, 60));
		$this->addFrontSystem(new LightRailGun(2, 6, 3, 300, 60));
		$this->addFrontSystem(new FlakCannon(2, 4, 2, 180, 360));
		$this->addFrontSystem(new FlakCannon(2, 4, 2, 180, 360));
		$this->addFrontSystem(new FlakCannon(2, 4, 2, 0, 180));
		$this->addFrontSystem(new FlakCannon(2, 4, 2, 0, 180));
        $this->addFrontSystem(new Thruster(2, 10, 0, 3, 1));
	    
        $this->addAftSystem(new Thruster(2, 8, 0, 3, 2));    
        $this->addAftSystem(new Thruster(2, 8, 0, 3, 2));    
		$this->addAftSystem(new FlakCannon(2, 4, 2, 180, 360));
		$this->addAftSystem(new FlakCannon(2, 4, 2, 180, 360));
		$this->addAftSystem(new FlakCannon(2, 4, 2, 0, 180));
		$this->addAftSystem(new FlakCannon(2, 4, 2, 0, 180));
       
        $this->addPrimarySystem(new Structure(3, 80));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			4 => "Thruster",
			7 => "Connection Strut",
			9 => "Targeting Array",
			12 => "Engine",
			15 => "Antiquated Scanner",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			4 => "Thruster",
			6 => "Light Railgun",
			10 => "Flak Cannon",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Thruster",
			10 => "Flak Cannon",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
