<?php
class ChoukaRaiderHighwaymanB_NoCargo extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 285;
        $this->faction = "ZEscalation Chouka Raider";
        $this->phpclass = "ChoukaRaiderHighwaymanB_NoCargo";
        $this->imagePath = "img/ships/EscalationWars/ChoukaRaiderHighwaymanNoCargo.png";
        $this->shipClass = "Highwayman-B Sloop (No Cargo)";
			$this->variantOf = "Highwayman-A Sloop";
			$this->occurence = "uncommon";
		$this->unofficial = true;
        $this->agile = true;		
        $this->canvasSize = 75;
	    $this->isd = 1821;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 10;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 3;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 60;
        
         
        $this->addPrimarySystem(new Reactor(2, 10, 0, 0));
        $this->addPrimarySystem(new CnC(3, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(2, 9, 4, 4));
        $this->addPrimarySystem(new Engine(2, 9, 0, 10, 2));
        $this->addPrimarySystem(new Hangar(2, 1));
        $this->addPrimarySystem(new Thruster(1, 8, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(1, 8, 0, 5, 4));     
        
        $this->addFrontSystem(new LightParticleCannon(1, 6, 5, 240, 60));
        $this->addFrontSystem(new LightParticleCannon(1, 6, 5, 300, 120));
		$this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 270, 90));
		$this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 270, 90));
        $this->addFrontSystem(new Thruster(1, 5, 0, 3, 1));
        $this->addFrontSystem(new Thruster(1, 5, 0, 3, 1));
	    
		$this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 90, 270));
        $this->addAftSystem(new Thruster(1, 7, 0, 5, 2));    
        $this->addAftSystem(new Thruster(1, 7, 0, 5, 2));    
       
        $this->addPrimarySystem(new Structure(2, 32));


	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			7 => "Thruster",
			10 => "Scanner",
			13 => "Engine",
			15 => "Hangar",
			18 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			5 => "Thruster",
			7 => "Light Particle Cannon",
			9 => "Light Particle Beam",
			16 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Thruster",
			8 => "Light Particle Beam",
			16 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
