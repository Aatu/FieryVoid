<?php
class DuxRaider extends BaseShip{
/*Raider Dux Jump Cruiser, from Variants-6*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 460;
        $this->faction = "Raiders";
        $this->phpclass = "DuxRaider";
        $this->imagePath = "img/ships/celerian.png";
        $this->shipClass = "Raider Dux Jump Cruiser";
        $this->shipSizeClass = 3;
        $this->limited = 10; //Restricted Deployment
        $this->fighters = array("heavy"=>12);
		$this->isd = 2002;
		$this->variantOf = "Centauri Privateer Dux Jump Cruiser";
        
		$this->notes = "Generic raider unit.";
		$this->notes .= "<br> ";

        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;       
         
        $this->addPrimarySystem(new Reactor(6, 12, 0, 0));
        $this->addPrimarySystem(new CnC(6, 14, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 3, 6));
        $this->addPrimarySystem(new Engine(5, 20, 0, 9, 3));	
	$this->addPrimarySystem(new CargoBay(3, 12));	
        
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new Hangar(4, 14));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
		
        $this->addAftSystem(new Thruster(2, 10, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 10, 0, 3, 2));
        $this->addAftSystem(new JumpEngine(4, 25, 3, 20));
        
	$this->addLeftSystem(new Thruster(4, 14, 0, 5, 3));
        $this->addLeftSystem(new LightParticleCannon(3, 6, 5, 240, 360));
        $this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));   
        $this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
                		
	$this->addRightSystem(new Thruster(4, 14, 0, 5, 4));
        $this->addRightSystem(new LightParticleCannon(3, 6, 5, 0, 120));
        $this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        $this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 38));
        $this->addAftSystem(new Structure( 4, 36));
        $this->addLeftSystem(new Structure( 4, 40));
        $this->addRightSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 5, 40));
		


	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			9 => "Structure",
			12 => "Scanner",
			15 => "Engine",
			17 => "Cargo Bay",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			5 => "Thruster",
			7 => "Light Particle Beam",
			10 => "Hangar",
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
			5 => "Thruster",
			8 => "Light Particle Cannon",
			10 => "Light Particle Beam",
			18 => "Structure",
			20 => "Primary",
		),

		4=> array(
			5 => "Thruster",
			8 => "Light Particle Cannon",
			10 => "Light Particle Beam",
			18 => "Structure",
			20 => "Primary",
		),

	);

		
    }
}
?>