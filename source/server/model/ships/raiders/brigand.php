<?php
class Brigand extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 375;
        $this->faction = "Raiders";
        $this->phpclass = "Brigand";
        $this->imagePath = "img/ships/warbird.png"; //need to change
        $this->shipClass = "Brigand Attack Cruiser";

        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 30;

        $this->addPrimarySystem(new Reactor(5, 12, 0, 4));
        $this->addPrimarySystem(new CnC(5, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 11, 2, 4));
        $this->addPrimarySystem(new Engine(4, 14, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(3, 1));
		$this->addPrimarySystem(new CargoBay(2, 8));
        $this->addPrimarySystem(new Thruster(3, 12, 0, 6, 1));
        $this->addPrimarySystem(new Thruster(3, 18, 0, 8, 2));

        $this->addLeftSystem(new MediumPlasma(2, 5, 3, 300, 360));
        $this->addLeftSystem(new LightParticleCannon(2, 6, 5, 240, 360));
        $this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 240, 360));
        $this->addLeftSystem(new Thruster(4, 11, 0, 3, 3));

        $this->addRightSystem(new MediumPlasma(2, 5, 3, 0, 60));
        $this->addRightSystem(new LightParticleCannon(2, 6, 5, 0, 120));
        $this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 120));
        $this->addRightSystem(new Thruster(4, 11, 0, 3, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(4, 33));
        $this->addLeftSystem(new Structure(3, 33));
        $this->addRightSystem(new Structure(3, 33));
        
        $this->hitChart = array(
        		0=> array(
        				1 => "structure",
        				2 => "structure",
        				3 => "structure",
        				4 => "structure",
        				5 => "structure",
        				6 => "structure",
        				7 => "structure",
        				8 => "structure",
        				9 => "thruster",
        				10 => "thruster",
        				11 => "thruster",
        				12 => "scanner",
        				13 => "scanner",
        				14 => "engine",
        				15 => "engine",
        				16 => "cargoBay",
        				17 => "hanger",
        				18 => "reactor",
        				19 => "reactor",
        				20 => "CnC",
        		),
        		3=> array(
        				1 => "thruster",
        				2 => "thruster",
        				3 => "thruster",
        				4 => "thruster",
        				5 => "thruster",
        				6 => "thruster",
        				7 => "lightParticleBeamShip",
        				8 => "lightParticleCannon",
        				9 => "lightParticleCannon",
        				10 => "mediumPlasma",
        				11 => "mediumPlasma",
        				12 => "structure",
        				13 => "structure",
        				14 => "structure",
        				15 => "structure",
        				16 => "structure",
        				17 => "structure",
        				18 => "structure",
        				19 => "primary",
        				20 => "primary",
        		),
        		4=> array(
        				1 => "thruster",
        				2 => "thruster",
        				3 => "thruster",
        				4 => "thruster",
        				5 => "thruster",
        				6 => "thruster",
        				7 => "lightParticleBeamShip",
        				8 => "lightParticleCannon",
        				9 => "lightParticleCannon",
        				10 => "mediumPlasma",
        				11 => "mediumPlasma",
        				12 => "structure",
        				13 => "structure",
        				14 => "structure",
        				15 => "structure",
        				16 => "structure",
        				17 => "structure",
        				18 => "structure",
        				19 => "primary",
        				20 => "primary",
        		),
        );
    }
}
?>