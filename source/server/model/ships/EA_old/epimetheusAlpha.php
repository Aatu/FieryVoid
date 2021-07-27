<?php
class EpimetheusAlpha extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 450;
        $this->faction = "EA (early)";
        $this->phpclass = "EpimetheusAlpha";
        $this->imagePath = "img/ships/epimetheus.png";
        $this->shipClass = "Epimetheus Jump Cruiser (Alpha)";
        $this->shipSizeClass = 3;
//			$this->canvasSize = 175; //img has 200px per side
 		$this->unofficial = true;
       
        $this->isd = 2164;

        $this->forwardDefense = 14;
        $this->sideDefense = 15;

        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 0;

        $this->addPrimarySystem(new Reactor(4, 16, 0, 0));
        $this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 4, 5));
        $this->addPrimarySystem(new Engine(4, 11, 0, 5, 4));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 360));
        $this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 360));

        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 360));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 0, 120));

        $this->addAftSystem(new Thruster(3, 6, 0, 1, 2));
        $this->addAftSystem(new Thruster(3, 7, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 6, 0, 1, 2));
        $this->addAftSystem(new JumpEngine(4, 20, 3, 30));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270));
        $this->addAftSystem(new MediumPlasma(3, 5, 3, 120, 240));

        $this->addLeftSystem(new Thruster(3, 11, 0, 3, 3));
		$this->addLeftSystem(new EWOMissileRack(3, 6, 0, 240, 360));
        $this->addLeftSystem(new MediumPlasma(3, 5, 3, 180, 360));

        $this->addRightSystem(new Thruster(3, 11, 0, 3, 4));
		$this->addRightSystem(new EWOMissileRack(3, 6, 0, 0, 120));
        $this->addRightSystem(new MediumPlasma(3, 5, 3, 0, 180));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 50));
        $this->addAftSystem(new Structure( 3, 36));
        $this->addLeftSystem(new Structure( 4, 48));
        $this->addRightSystem(new Structure( 4, 48));
        $this->addPrimarySystem(new Structure( 4, 40));
		
        $this->hitChart = array(
        	0=> array(
        		10 => "Structure",
        		12 => "Light Particle Beam",
        		14 => "Scanner",
				16 => "Engine",
        		18 => "Hangar",
        		19 => "Reactor",
        		20 => "C&C",	
        	),
        	1=> array(
        		5 => "Thruster",
        		7 => "Medium Plasma Cannon",
        		10 => "Light Particle Beam",
        		18 => "Structure",
        		20 => "Primary",        			
        	),
        	2=> array(
        		6 => "Thruster",
        		8 => "Medium Plasma Cannon",
        		9 => "Light Particle Beam",
				12 => "Jump Engine",
        		18 => "Structure",
        		20 => "Primary",           			
        	),
        	3=> array(
        		4 => "Thruster",
        		6 => "Medium Plasma Cannon",
				10 => "Class-O Missile Rack",
        		18 => "Structure",
        		20 => "Primary",           			
        	),			
        	4=> array(
        		4 => "Thruster",
        		6 => "Medium Plasma Cannon",
				10 => "Class-O Missile Rack",
        		18 => "Structure",
        		20 => "Primary",           				
        	),			
		);		
		
    }
}

?>
