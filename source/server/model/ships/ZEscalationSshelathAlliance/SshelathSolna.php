<?php

class SshelathSolna extends BaseShipNoAft{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 350;
        $this->faction = "ZEscalation Sshel'ath Alliance";
        $this->phpclass = "SshelathSolna";
        $this->imagePath = "img/ships/EscalationWars/SshelathSolna.png";
        $this->shipClass = "Solna Light Cruiser";
        $this->isd = 1957;
        $this->canvasSize = 160;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;

        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        
        $this->iniativebonus = 30;

        $this->addPrimarySystem(new Reactor(4, 11, 0, 0));
        $this->addPrimarySystem(new CnC(4, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 3, 5));
        $this->addPrimarySystem(new Engine(4, 11, 0, 8, 4));
        $this->addPrimarySystem(new Hangar(3, 2));
        $this->addPrimarySystem(new Thruster(3, 18, 0, 8, 2));

        $this->addFrontSystem(new Railgun(2, 9, 6, 270, 90));
		$this->addFrontSystem(new LightLaser(2, 4, 3, 240, 360));
		$this->addFrontSystem(new LightLaser(2, 4, 3, 0, 120));
        $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));

        $this->addLeftSystem(new LaserCutter(2, 6, 4, 300, 360));
        $this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
        $this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 120, 300));
		$this->addLeftSystem(new LightLaser(2, 4, 3, 180, 300));
        $this->addLeftSystem(new Thruster(2, 10, 0, 3, 3));

        $this->addRightSystem(new LaserCutter(2, 6, 4, 0, 60));
        $this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));
        $this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 60, 240));
		$this->addRightSystem(new LightLaser(2, 4, 3, 60, 180));
        $this->addRightSystem(new Thruster(2, 10, 0, 3, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 32));
        $this->addLeftSystem(new Structure( 3, 30));
        $this->addRightSystem(new Structure( 3, 30));
        $this->addPrimarySystem(new Structure( 4, 30));
    
            $this->hitChart = array(
        		0=> array(
        				8 => "Structure",
        				11 => "Thruster",
        				13 => "Scanner",
        				15 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				4 => "Thruster",
        				7 => "Railgun",
        				9 => "Light Laser",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				4 => "Thruster",
        				6 => "Laser Cutter",
        				7 => "Light Laser",
                        10 => "Light Particle Beam",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				4 => "Thruster",
        				6 => "Laser Cutter",
        				7 => "Light Laser",
                        10 => "Light Particle Beam",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    
    }
}
?>
