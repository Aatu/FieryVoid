<?php
class Strela2008 extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 470;
        $this->faction = "Centauri (WotCR)";
        $this->phpclass = "Strela2008";
        $this->imagePath = "img/ships/strela.png";
        $this->shipClass = "Strela Light Jump Ship (2008)";
			$this->variantOf = "Strela Light Jump Ship";
        $this->fighters = array("heavy"=>6);
	    $this->isd = 2008;
		$this->limited = 33;  //Limited Deployment
		
        
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 30;
         
        $this->addPrimarySystem(new Reactor(6, 17, 0, 0));
        $this->addPrimarySystem(new CnC(6, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 3, 7));
        $this->addPrimarySystem(new Engine(5, 15, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(5, 7));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 4));
		
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));
        $this->addFrontSystem(new AssaultLaser(4, 6, 4, 240, 360));
        $this->addFrontSystem(new AssaultLaser(4, 6, 4, 0, 120));
        
        $this->addAftSystem(new Thruster(4, 14, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 14, 0, 4, 2));
        $this->addAftSystem(new JumpEngine(3, 15, 3, 25));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        $this->addAftSystem(new AssaultLaser(3, 6, 4, 120, 240));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 50));
        $this->addAftSystem(new Structure( 4, 50));
        $this->addPrimarySystem(new Structure( 5, 40 ));

        $this->hitChart = array(
            0=> array(
                    6 => "Structure",
                    9 => "Thruster",
                    12 => "Scanner",
                    15 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
                    7 => "Assault Laser",
                    9 => "Light Particle Beam",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    4 => "Thruster",
                    6 => "Assault Laser",
                    8 => "Light Particle Beam",
					9 => "Jump Engine",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
