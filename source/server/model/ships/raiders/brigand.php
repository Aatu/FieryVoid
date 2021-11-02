<?php
class Brigand extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 375;
        $this->faction = "Raiders";
        $this->phpclass = "Brigand";
        $this->imagePath = "img/ships/drazi/DraziWarbird.png"; 
        $this->shipClass = "Brigand Attack Cruiser";

		$this->notes = "Generic raider unit.";
		$this->notes .= "<br> ";

		$this->isd = 1984;
        
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
        				8 => "Structure",
        				11 => "Thruster",
        				13 => "Scanner",
        				15 => "engine",
        				16 => "Cargo Bay",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C"
        		),
        		3=> array(
        				6 => "Thruster",
        				7 => "Light Particle Beam",
        				9 => "Light Particle Cannon",
        				10 => "Medium Plasma",
        				18 => "Structure",
        				20 => "Primary"
        		),
        		4=> array(
        				6 => "Thruster",
        				7 => "Light Particle Beam",
        				8 => "Light Particle Cannon",
        				9 => "Light Particle Cannon",
        				1 => "Medium Plasma",
        				18 => "Structure",
        				20 => "Primary"
        		),
        );
    }
}
?>
