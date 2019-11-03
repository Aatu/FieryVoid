<?php
class Celerian extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 650;
        $this->faction = "Centauri (WotCR)";
        $this->phpclass = "Celerian";
        $this->imagePath = "img/ships/celerian.png";
        $this->shipClass = "Celerian Warcruiser (2007)";
        	$this->variantOf = "Celerian Warcruiser";
        $this->shipSizeClass = 3;
	    $this->isd = 2007;
    //    $this->fighters = array("normal"=>12);
        
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;    
        $this->iniativebonus = 5;   //+1 Ini!   
         
        $this->addPrimarySystem(new Reactor(6, 16, 0, 0));
        $this->addPrimarySystem(new CnC(6, 14, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 18, 4, 8));
        $this->addPrimarySystem(new Engine(5, 20, 0, 9, 3));
	$this->addPrimarySystem(new Hangar(4, 2));		
        
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new HeavyPlasma(3, 8, 5, 300, 360));
        $this->addFrontSystem(new AssaultLaser(3, 6, 4, 300, 60));
        $this->addFrontSystem(new HeavyPlasma(3, 8, 5, 0, 60));
		
        $this->addAftSystem(new Thruster(3, 10, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 3, 2));
        $this->addAftSystem(new JumpEngine(5, 25, 3, 20));
        
	$this->addLeftSystem(new Thruster(4, 14, 0, 5, 3));
        $this->addLeftSystem(new AssaultLaser(3, 6, 4, 240, 360));
        $this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));   
        $this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360)); 
        $this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));   
                		
	$this->addRightSystem(new Thruster(4, 14, 0, 5, 4));
        $this->addRightSystem(new AssaultLaser(3, 6, 4, 0, 120));
        $this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        $this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        $this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 38));
        $this->addAftSystem(new Structure( 4, 36));
        $this->addLeftSystem(new Structure( 4, 40));
        $this->addRightSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 5, 40));
		


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
			5 => "Thruster",
			7 => "Assault Laser",
			10 => "Heavy Plasma Cannon",
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
			4 => "Thruster",
			6 => "Assault Laser",
			10 => "Light Particle Beam",
			18 => "Structure",
			20 => "Primary",
		),

		4=> array(
			4 => "Thruster",
			6 => "Assault Laser",
			10 => "Light Particle Beam",
			18 => "Structure",
			20 => "Primary",
		),

	);

		
    }
}
?>
