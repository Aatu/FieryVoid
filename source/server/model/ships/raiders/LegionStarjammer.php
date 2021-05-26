<?php
class LegionStarjammer extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
  		$this->pointCost = 850;
  		$this->faction = "Raiders";
        $this->phpclass = "LegionStarjammer";
        $this->imagePath = "img/ships/dragonship.png";
        $this->shipClass = "Legion Starjammer Heavy Cruiser";
			$this->occurence = "uncommon";
			$this->variantOf = "Dragonship";        
		$this->shipSizeClass = 3;
        $this->fighters = array("heavy"=>12);

		$this->notes = "Used only by the Imperial Star Legion";
		$this->notes .= "<br>Provides +5 initiative to all friendly Raider units within 5 hexes.";
        
        $this->forwardDefense = 15;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;

		$this->iniativebonus = 5;
   
        $this->addPrimarySystem(new Reactor(5, 25, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 1));
        $this->addPrimarySystem(new Scanner(5, 17, 5, 7));
        $this->addPrimarySystem(new Engine(5, 20, 0, 12, 2));
        $this->addPrimarySystem(new Hangar(5, 14));
        $this->addPrimarySystem(new JumpEngine(5, 15, 4, 24));
  
        $this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));        
		$this->addFrontSystem(new HeavyLaser(4, 8, 6, 300, 60));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 240, 60));
        $this->addFrontSystem(new PlasmaAccelerator(4, 10, 5, 300, 60));
        $this->addFrontSystem(new MediumPulse(4, 6, 3, 300, 60));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 300, 120));
		$this->addFrontSystem(new HeavyLaser(4, 8, 6, 300, 60));

        $this->addAftSystem(new Thruster(3, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 16, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 16, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 2, 2));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 120, 300));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 60, 240));
        
  		$this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));
		$this->addLeftSystem(new MediumPulse(3, 6, 3, 180, 360));
		$this->addLeftSystem(new TwinArray(2, 6, 2, 180, 360));
		
  		$this->addRightSystem(new Thruster(4, 15, 0, 5, 4));
  		$this->addRightSystem(new MediumPulse(3, 6, 3, 0, 180));
  		$this->addRightSystem(new TwinArray(2, 6, 2, 0, 180));
  		
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 52));
        $this->addAftSystem(new Structure( 4, 52));
        $this->addLeftSystem(new Structure( 5, 52));
        $this->addRightSystem(new Structure( 5, 52));
        $this->addPrimarySystem(new Structure( 5, 55));
        
        $this->hitChart = array(
        		0=> array(
        				8 => "Structure",
        				10 => "Jump Engine",
        				13 => "Scanner",
        				15 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				4 => "Thruster",
        				6 => "Medium Pulse Cannon",
        				8 => "Plasma Accelerator",
        				10 => "Heavy Laser",
						12 => "Standard Particle Beam",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				8 => "Thruster",
        				10 => "Standard Particle Beam",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				4 => "Thruster",
        				5 => "Medium Pulse Cannon",
        				7 => "Twin Array",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				4 => "Thruster",
        				5 => "Medium Pulse Cannon",
        				7 => "Twin Array",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
