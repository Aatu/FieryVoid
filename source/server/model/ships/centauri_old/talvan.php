<?php
/*NOT FINISHED YET*/
class Talvan extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 600;
        $this->faction = "Centauri (WotCR)";
        $this->phpclass = "Talvan";
        $this->imagePath = "img/ships/centurion.png";
        $this->shipClass = "Talvan Attack Cruiser";
        //$this->variantOf = "Talvan Attack Cruiser";
        $this->shipSizeClass = 3;
        //$this->limited = 33; //limited deployment
        //$this->fighters = array("heavy"=>12);
	    $this->isd = 1860;
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
        $this->addPrimarySystem(new Scanner(6, 20, 4, 7));
        $this->addPrimarySystem(new Engine(5, 20, 0, 10, 3));
		$this->addPrimarySystem(new Hangar(5, 2));
        
        $this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));
        $this->addFrontSystem(new LightParticleBeamShip(3, 2, 1, 240, 120));
		$this->addFrontSystem(new AssaultLaser(4, 6, 4, 300, 60));
        $this->addFrontSystem(new AssaultLaser(4, 6, 4, 300, 60));
        $this->addFrontSystem(new HeavyPlasma(3, 8, 5, 300, 60));
        $this->addFrontSystem(new HeavyPlasma(3, 8, 5, 300, 60));
		
        $this->addAftSystem(new Thruster(4, 10, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 2, 2));
	$this->addAftSystem(new JumpEngine(6, 24, 4, 20));      
        $this->addAftSystem(new LightParticleBeamShip(3, 2, 1, 90, 270));  
        $this->addAftSystem(new AssaultLaser(4, 6, 4, 180, 300));
	$this->addAftSystem(new AssaultLaser(4, 6, 4, 120, 240));
        $this->addAftSystem(new AssaultLaser(4, 6, 4, 120, 240));
        $this->addAftSystem(new AssaultLaser(4, 6, 4, 60, 180));
	    
		$this->addLeftSystem(new Thruster(4, 20, 0, 6, 3));
        $this->addLeftSystem(new AssaultLaser(4, 6, 4, 240, 360));
        $this->addLeftSystem(new LightParticleBeamShip(3, 2, 1, 180, 360));
        $this->addLeftSystem(new LightParticleBeamShip(3, 2, 1, 180, 360));
        $this->addLeftSystem(new LightParticleBeamShip(3, 2, 1, 180, 360));
        $this->addLeftSystem(new LightParticleBeamShip(3, 2, 1, 180, 360));
	    
		$this->addRightSystem(new Thruster(4, 20, 0, 6, 4));
        $this->addRightSystem(new AssaultLaser(4, 6, 4, 0, 120));
        $this->addRightSystem(new LightParticleBeamShip(3, 2, 1, 0, 180));
        $this->addRightSystem(new LightParticleBeamShip(3, 2, 1, 0, 180));
        $this->addRightSystem(new LightParticleBeamShip(3, 2, 1, 0, 180));
        $this->addRightSystem(new LightParticleBeamShip(3, 2, 1, 0, 180));
       
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 55));
        $this->addAftSystem(new Structure( 5, 55));
        $this->addLeftSystem(new Structure( 5, 60));
        $this->addRightSystem(new Structure( 5, 60));
        $this->addPrimarySystem(new Structure( 6, 56));
	    
	    
	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			9 => "Structure",
			12 => "Scanner",
			15 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),
		1=> array(
			5 => "Thruster",
			8 => "Assault Laser",
			9 => "Light Particle Beam",
			11 => "Heavy Plasma Cannon",
			18 => "Structure",
			20 => "Primary",
		),
		2=> array(
			5 => "Thruster",
			8 => "Jump Engine",
			13 => "Assault Laser",
			14 => "Light Particle Beam",
			18 => "Structure",
			20 => "Primary",
		),
		3=> array(
			5 => "Thruster",
			7 => "Assault Laser",
			10 => "Light Particle Beam",
			18 => "Structure",
			20 => "Primary",
		),
		4=> array(
			5 => "Thruster",
			7 => "Assault Laser",
			10 => "Light Particle Beam",
			18 => "Structure",
			20 => "Primary",
		),
	);
	    
    }
}
?>
