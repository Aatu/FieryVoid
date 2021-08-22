<?php
class Talvan1990 extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 625;
        $this->faction = "Centauri (WotCR)";
        $this->phpclass = "Talvan1990";
        $this->imagePath = "img/ships/centurion.png";
        $this->shipClass = "Talvan Attack Cruiser (1990)";
			$this->variantOf = "Talvan Attack Cruiser";
        $this->shipSizeClass = 3;
        //$this->limited = 33; //limited deployment
        //$this->fighters = array("heavy"=>12);
	    $this->isd = 1990;
        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        
        $this->turncost = 1;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 1*5;  
         
        $this->addPrimarySystem(new Reactor(6, 22, 0, 0));
        $this->addPrimarySystem(new CnC(6, 18, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 20, 4, 8));
        $this->addPrimarySystem(new Engine(5, 20, 0, 10, 3));
		$this->addPrimarySystem(new Hangar(5, 2));
        
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new ImperialLaser(3, 8, 5, 300, 60));
        $this->addFrontSystem(new LightParticleBeamShip(3, 2, 1, 240, 60));
        $this->addFrontSystem(new LightParticleBeamShip(3, 2, 1, 240, 60));
        $this->addFrontSystem(new LightParticleBeamShip(3, 2, 1, 180, 60));
        $this->addFrontSystem(new LightParticleBeamShip(3, 2, 1, 300, 180));
        $this->addFrontSystem(new LightParticleBeamShip(3, 2, 1, 300, 120));
        $this->addFrontSystem(new LightParticleBeamShip(3, 2, 1, 300, 120));
		
        $this->addAftSystem(new Thruster(4, 14, 0, 5, 2));
        $this->addAftSystem(new Thruster(4, 14, 0, 5, 2));
		$this->addAftSystem(new JumpEngine(4, 20, 3, 20));      
	    
		$this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));
        $this->addLeftSystem(new HeavyPlasma(3, 8, 5, 300, 0));
        $this->addLeftSystem(new TacLaser(3, 5, 4, 240, 360));
	    
		$this->addRightSystem(new Thruster(4, 15, 0, 5, 4));
        $this->addRightSystem(new HeavyPlasma(3, 8, 5, 0, 60));
        $this->addRightSystem(new TacLaser(3, 5, 4, 0, 120));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 42));
        $this->addAftSystem(new Structure( 4, 40));
        $this->addLeftSystem(new Structure( 4, 54));
        $this->addRightSystem(new Structure( 4, 54));
        $this->addPrimarySystem(new Structure( 6, 36));
	    
	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			10 => "Structure",
			13 => "Scanner",
			16 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),
		1=> array(
			3 => "Thruster",
			5 => "Imperial Laser",
			9 => "Light Particle Beam",
			18 => "Structure",
			20 => "Primary",
		),
		2=> array(
			7 => "Thruster",
			12 => "Jump Engine",
			18 => "Structure",
			20 => "Primary",
		),
		3=> array(
			3 => "Thruster",
			6 => "Heavy Plasma Cannon",
			9 => "Tactical Laser",
			18 => "Structure",
			20 => "Primary",
		),
		4=> array(
			3 => "Thruster",
			6 => "Heavy Plasma Cannon",
			9 => "Tactical Laser",
			18 => "Structure",
			20 => "Primary",
		),
	);
	    
    }
}
?>