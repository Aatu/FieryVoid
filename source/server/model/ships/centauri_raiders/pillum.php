<?php
class Pillum extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
  		$this->pointCost = 400;
  		$this->faction = "Raiders";
        $this->phpclass = "Pillum";
        $this->imagePath = "img/ships/raiderPillager.png";
        $this->shipClass = "Centauri Privateer Pillum Command Ship";
        $this->shipSizeClass = 3;
        $this->fighters = array("Normal"=>12);
        $this->limited = 33; //Restricted Deployment
        
		$this->notes = "Used only by Centauri Privateers";
//		$this->notes .= "<br> ";

		$this->isd = 1972;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1.5;
        $this->accelcost = 3;
        $this->rollcost = 1;
        $this->pivotcost = 5;

   
        $this->addPrimarySystem(new Reactor(4, 16, 0, 3));
        $this->addPrimarySystem(new CnC(5, 15, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 3, 5));
        $this->addPrimarySystem(new Hangar(3, 13));
        $this->addPrimarySystem(new JumpEngine(4, 12, 3, 40));
  
        $this->addFrontSystem(new Thruster(3, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 6, 0, 2, 1));
        $this->addFrontSystem(new TacLaser(2, 5, 4, 240, 60));
        $this->addFrontSystem(new ParticleProjector(2, 6, 1, 240, 120));
        $this->addFrontSystem(new TacLaser(2, 5, 4, 300, 120));
        
        $this->addAftSystem(new Thruster(3, 10, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 2, 2));
		$this->addAftSystem(new Engine(3, 10, 0, 6, 3));
		$this->addAftSystem(new CargoBay(2, 15));
		$this->addAftSystem(new CargoBay(2, 15));        
		$this->addAftSystem(new TacLaser(2, 5, 4, 120, 240));
		
  		$this->addLeftSystem(new Thruster(3, 15, 0, 4, 3));
		$this->addLeftSystem(new ParticleProjector(2, 6, 1, 180, 360));
		$this->addLeftSystem(new MediumPlasma(2, 5, 3, 180, 360));
		
  		$this->addRightSystem(new Thruster(3, 15, 0, 4, 4));
		$this->addRightSystem(new ParticleProjector(2, 6, 1, 0, 180));
		$this->addRightSystem(new MediumPlasma(2, 5, 3, 0, 180));
  		
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 38));
        $this->addAftSystem(new Structure( 3, 36));
        $this->addLeftSystem(new Structure( 3, 30));
        $this->addRightSystem(new Structure( 3, 30));
        $this->addPrimarySystem(new Structure( 4, 36));
        
        $this->hitChart = array(
        		0=> array(
        				9 => "Structure",
        				11 => "Scanner",
        				13 => "Jump Engine",
        				16 => "Hangar",
        				18 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				5 => "Thruster",
        				8 => "Tactical Laser",
        				10 => "Particle Projector",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				7 => "Tactical Laser",
        				9 => "Engine",
        				11 => "Cargo Bay",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				6 => "Thruster",
        				7 => "Particle Projector",
        				9 => "Medium Plasma Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				6 => "Thruster",
        				7 => "Particle Projector",
        				9 => "Medium Plasma Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
