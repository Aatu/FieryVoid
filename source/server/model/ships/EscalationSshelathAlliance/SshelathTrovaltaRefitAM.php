<?php
class SshelathTrovaltaRefitAM extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 200;
        $this->faction = "Escalation Wars Sshel'ath Alliance";
        $this->phpclass = "SshelathTrovaltaRefitAM";
        $this->imagePath = "img/ships/EscalationWars/SshelathTrovalta.png";
        $this->shipClass = "Trovalta Frigate (1928 refit)";
			$this->variantOf = "Trovalta Frigate";
			$this->occurence = "common";		
		$this->unofficial = true;
        $this->canvasSize = 90;
	    $this->isd = 1928;

	    $this->notes = 'Mst-as Faction';
	    $this->notes .= '<br>Atmospheric capable';
        
        $this->forwardDefense = 12;
        $this->sideDefense = 13;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 60;

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(12); //pass magazine capacity - class-SO launchers hold 12 rounds per mount
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 12); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_D';//add enhancement options for other missiles - Class-D
        
        $this->addPrimarySystem(new Reactor(3, 10, 0, 0));
        $this->addPrimarySystem(new CnC(3, 5, 0, 0));
        $this->addPrimarySystem(new AntiquatedScanner(3, 7, 2, 3));
        $this->addPrimarySystem(new Hangar(3, 1));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 2, 3));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 2, 4));        

		$this->addFrontSystem(new EWDefenseLaser(1, 2, 1, 180, 60));
		$this->addFrontSystem(new NexusLightLaserCutter(2, 4, 3, 300, 360));
		$this->addFrontSystem(new AmmoMissileRackSO(2, 0, 0, 270, 90, $ammoMagazine, false));
		$this->addFrontSystem(new NexusLightLaserCutter(2, 4, 3, 0, 60));
		$this->addFrontSystem(new EWDefenseLaser(1, 2, 1, 300, 180));
        $this->addFrontSystem(new Thruster(1, 4, 0, 1, 1));
        $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(1, 4, 0, 1, 1));
	    
		$this->addAftSystem(new EWDefenseLaser(1, 2, 1, 60, 300));
        $this->addAftSystem(new Engine(2, 11, 0, 8, 2));
        $this->addAftSystem(new Thruster(2, 12, 0, 8, 2));    
       
        $this->addPrimarySystem(new Structure(2, 32));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			9 => "Thruster",
			12 => "Scanner",
			15 => "Hangar",
			18 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			4 => "Thruster",
			6 => "Class-SO Missile Rack",
			8 => "Light Laser Cutter",
			10 => "Defense Laser",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			5 => "Thruster",
			7 => "Defense Laser",
			9 => "Engine",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
