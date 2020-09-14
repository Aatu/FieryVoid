<?php
class AlacanTacomiPatrolCutter extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 300;
	$this->faction = "Small Races";
        $this->phpclass = "AlacanTacomiPatrolCutter";
        $this->imagePath = "img/ships/AlacanTacomi.png";
			$this->canvasSize = 125; //img has 125px per side
        $this->shipClass = "Alacan Tacomi Patrol Cutter";

	$this->isd = 2208;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 1;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;
        
        $this->addPrimarySystem(new Reactor(5, 8, 0, 0));
        $this->addPrimarySystem(new CnC(5, 5, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 10, 3, 5));
        $this->addPrimarySystem(new Engine(5, 9, 0, 6, 2));
		$this->addPrimarySystem(new Hangar(5, 1));
        $this->addPrimarySystem(new Thruster(4, 8, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(4, 8, 0, 3, 4));
   
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new LaserCutter(3, 6, 4, 300, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));

        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 120, 300));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 60, 240));
        $this->addAftSystem(new Thruster(4, 10, 0, 6, 2));


        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(5, 50));
		
		$this->hitChart = array(
			0=> array(
					8 => "Thruster",
					10 => "Hangar",
					13 => "Scanner",
					16 => "Engine",
					18 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					6 => "Laser Cutter",
					9 => "Light Particle Beam",
					17 => "Structure",
					20 => "Primary",
			),
			2=> array(
					4 => "Thruster",
					6 => "Light Particle Beam",
					17 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
