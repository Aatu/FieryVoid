<?php
class Rulnar extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 625;
        $this->faction = "Descari";
        $this->phpclass = "Rulnar";
        $this->imagePath = "img/ships/DescariRulnar.png";
        $this->shipClass = "Rulnar Heavy Destroyer";
	    $this->isd = 2250;
        
        
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;
        
         
        $this->addPrimarySystem(new Reactor(5, 16, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 4, 6));
        $this->addPrimarySystem(new Engine(5, 15, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 4, 4));   
        
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new HeavyPlasmaBolter(4, 0, 0, 240, 0));
        $this->addFrontSystem(new HeavyPlasmaBolter(4, 0, 0, 0, 120));
        $this->addFrontSystem(new MediumLaser(4, 6, 5, 240, 0));
        $this->addFrontSystem(new MediumLaser(4, 6, 5, 0, 120));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));        
		
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new LightPlasmaBolter(3, 6, 2, 180, 300));
        $this->addAftSystem(new LightPlasmaBolter(3, 6, 2, 60, 180));       
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));       
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 48));
        $this->addAftSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 4, 34));
		
			
		
		$this->hitChart = array(
			0=> array(
				8 => "Structure",
				11 => "Thruster",
				13 => "Scanner",
				15 => "Engine",
				16 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				3 => "Thruster",
				5 => "Medium Laser",
				7 => "Heavy Plasma Bolter",
				9 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				6 => "Thruster",
				8 => "Light Plasma Bolter",
				10 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
		); 
    }
}



?>