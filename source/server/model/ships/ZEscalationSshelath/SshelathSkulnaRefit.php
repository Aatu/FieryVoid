<?php
class SshelathSkulnaRefit extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 400;
	$this->faction = "ZEscalation Sshelath";
        $this->phpclass = "SshelathSkulnaRefit";
        $this->imagePath = "img/ships/EscalationWars/SshelathSkraltna.png";
        $this->shipClass = "Skrulna Carrier (1944 Refit)";
			$this->variantOf = "Skraltna Assault Cruiser";
			$this->occurence = "uncommon";		
		$this->shipSizeClass = 3;
		$this->canvasSize = 160; //img has 200px per side
		$this->unofficial = true;

		$this->fighters = array("medium"=>12, "light"=>12);

		$this->isd = 1944;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(3, 13, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 3, 5));
        $this->addPrimarySystem(new Engine(3, 11, 0, 9, 5));
		$this->addPrimarySystem(new Hangar(3, 14));
		
        $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));
		$this->addFrontSystem(new RailGun(2, 9, 6, 180, 60));
		$this->addFrontSystem(new RailGun(2, 9, 6, 300, 180));
		$this->addFrontSystem(new LightRailGun(2, 6, 3, 300, 60));

        $this->addAftSystem(new Thruster(2, 26, 0, 9, 2));
		$this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 90, 270));
		$this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 90, 270));
		$this->addAftSystem(new Hangar(2, 6));
		$this->addAftSystem(new Hangar(2, 6));

        $this->addLeftSystem(new LightRailGun(2, 6, 3, 240, 360));
		$this->addLeftSystem(new LightParticleBeamShip(1, 2, 1, 180, 360));
		$this->addLeftSystem(new LightParticleBeamShip(1, 2, 1, 180, 360));
		$this->addLeftSystem(new LightParticleBeamShip(1, 2, 1, 180, 360));
        $this->addLeftSystem(new Thruster(2, 13, 0, 4, 3));

        $this->addRightSystem(new LightRailGun(2, 6, 3, 0, 120));
		$this->addRightSystem(new LightParticleBeamShip(1, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(1, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(1, 2, 1, 0, 180));
        $this->addRightSystem(new Thruster(2, 13, 0, 4, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(3, 36));
        $this->addAftSystem(new Structure(3, 35));
        $this->addLeftSystem(new Structure(3, 32));
        $this->addRightSystem(new Structure(3, 32));
        $this->addPrimarySystem(new Structure(3, 30));
		
		$this->hitChart = array(
			0=> array(
					10 => "Structure",
					12 => "Scanner",
					14 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					3 => "Thruster",
					7 => "Railgun",
					9 => "Light Railgun",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Hangar",
					10 => "Light Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					6 => "Light Railgun",
					9 => "Light Particle Gun",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					6 => "Light Railgun",
					9 => "Defense Laser",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
