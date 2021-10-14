<?php
class Nalor extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 600;
        $this->faction = "Centauri (WotCR)";
        $this->phpclass = "Nalor";
        $this->imagePath = "img/ships/nalor.png";
        $this->shipClass = "Nalor Armored Cruiser";
        $this->shipSizeClass = 3;
    //    $this->fighters = array("normal"=>12);
	    
        $this->variantOf = "Celerian Warcruiser";
	    $this->isd = 1975;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->occurence = "uncommon";    
         
        $this->addPrimarySystem(new Reactor(6, 16, 0, 4));
        $this->addPrimarySystem(new CnC(6, 14, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 18, 4, 8));
        $this->addPrimarySystem(new Engine(5, 20, 0, 9, 3));
	$this->addPrimarySystem(new Hangar(4, 2));		
        
        $this->addFrontSystem(new Thruster(5, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(5, 10, 0, 4, 1));
        $this->addFrontSystem(new HeavyPlasma(4, 8, 5, 300, 360));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
        $this->addFrontSystem(new HeavyPlasma(4, 8, 5, 0, 60));
		
        $this->addAftSystem(new Thruster(4, 10, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 3, 2));
        
	$this->addLeftSystem(new Thruster(4, 14, 0, 5, 3));
        $this->addLeftSystem(new MediumPlasma(3, 5, 3, 240, 360));
	$this->addLeftSystem(new ParticleProjector(2, 6, 1, 180, 360));
        $this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360)); 
        $this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        
	$this->addRightSystem(new Thruster(4, 14, 0, 5, 4));
        $this->addRightSystem(new MediumPlasma(3, 5, 3, 0, 120));
        $this->addRightSystem(new ParticleProjector(2, 6, 1, 0, 180));
        $this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180)); 
        $this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));       
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 38));
        $this->addAftSystem(new Structure( 5, 36));
        $this->addLeftSystem(new Structure( 5, 40));
        $this->addRightSystem(new Structure( 5, 40));
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
			7 => "Medium Plasma Cannon",
			10 => "Heavy Plasma Cannon",
			18 => "Structure",
			20 => "Primary",
		),

		2=> array(
			7 => "Thruster",
			18 => "Structure",
			20 => "Primary",
		),

		3=> array(
			4 => "Thruster",
			6 => "Medium Plasma Cannon",
			8 => "Particle Projector",
			10 => "Light Particle Beam",
			18 => "Structure",
			20 => "Primary",
		),

		4=> array(
			4 => "Thruster",
			6 => "Medium Plasma Cannon",
			8 => "Particle Projector",
			10 => "Light Particle Beam",
			18 => "Structure",
			20 => "Primary",
		),

	);
	
		
    }
}
?>
