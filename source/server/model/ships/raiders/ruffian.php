<?php
class Ruffian extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
  		$this->pointCost = 300;
  		$this->faction = "Raiders";
        $this->phpclass = "Ruffian";
        $this->imagePath = "img/ships/battlewagon.png"; //needs to be changed
        $this->shipClass = "Ruffian Cruiser";
        $this->shipSizeClass = 3;
        $this->fighters = array("normal"=>18);
        
		$this->notes = "Generic raider unit.";
		$this->notes .= "<br> ";

		$this->isd = 1741;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1.5;
        $this->accelcost = 3;
        $this->rollcost = 1;
        $this->pivotcost = 5;
        $this->iniativebonus = -5;
         
        $this->addPrimarySystem(new Reactor(4, 14, 0, 0));
        $this->addPrimarySystem(new CnC(5, 15, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 3, 3));
        $this->addPrimarySystem(new Hangar(3, 13));
		$this->addPrimarySystem(new CargoBay(2, 15));
		$this->addPrimarySystem(new CargoBay(2, 15));
		
        $this->addFrontSystem(new Thruster(3, 6, 0, 1, 1));
        $this->addFrontSystem(new Thruster(3, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 6, 0, 1, 1));
		$this->addFrontSystem(new ParticleProjector(2, 6, 1, 240, 60));
		$this->addFrontSystem(new MediumPlasma(2, 5, 3, 240, 120));
		$this->addFrontSystem(new ParticleProjector(2, 6, 1, 300, 120));
        
        $this->addAftSystem(new Thruster(3, 10, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 2, 2));
        $this->addAftSystem(new Hangar(2, 3));
        $this->addAftSystem(new Hangar(2, 3));
        $this->addAftSystem(new Engine(4, 10, 0, 6, 3));
        
        $this->addLeftSystem(new Thruster(3, 13, 0, 3, 3));
		$this->addLeftSystem(new MediumPlasma(2, 5, 3, 180, 360));
		$this->addLeftSystem(new ParticleProjector(2, 6, 1, 180, 360));
		
		$this->addRightSystem(new Thruster(3, 13, 0, 3, 4));
        $this->addRightSystem(new MediumPlasma(2, 5, 3, 0, 180));
        $this->addRightSystem(new ParticleProjector(2, 6, 1, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 32));
        $this->addAftSystem(new Structure( 3, 36));
        $this->addLeftSystem(new Structure( 3, 30));
        $this->addRightSystem(new Structure( 3, 30));
        $this->addPrimarySystem(new Structure( 4, 30));
        
        $this->hitChart = array(
        		0=> array(
        				10 => "Structure",
        				12 => "Scanner",
        				14 => "Cargo Bay",
						16 => "Hangar",
        				18 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				5 => "Thruster",
        				8 => "Medium Plasma Cannon",
						10 => "Particle Projector",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				8 => "Engine",
        				10 => "Hangar",
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
