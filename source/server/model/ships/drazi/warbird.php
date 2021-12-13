<?php
class Warbird extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 480;
	$this->faction = "Drazi";
        $this->phpclass = "Warbird";
        $this->imagePath = "img/ships/drazi/DraziWarbird.png";
        $this->shipClass = "Warbird Cruiser";
	    $this->isd = 2234;
        $this->canvasSize = 160;

        $this->forwardDefense = 13;
        $this->sideDefense = 12;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 40;

        $this->addPrimarySystem(new Reactor(5, 12, 0, 2));
        $this->addPrimarySystem(new CnC(5, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 13, 4, 8));
        $this->addPrimarySystem(new Engine(5, 11, 0, 8, 2));
        $this->addPrimarySystem(new Hangar(4, 1));
        $this->addFrontSystem(new ParticleRepeater(4, 6, 4, 240, 120));
        $this->addAftSystem(new Thruster(4, 13, 0, 4, 1));
        $this->addAftSystem(new Thruster(5, 19, 0, 8, 2));

        $this->addLeftSystem(new ParticleCannon(4, 8, 7, 240, 0));
        $this->addLeftSystem(new ParticleCannon(4, 8, 7, 240, 0));
        $this->addLeftSystem(new StdParticleBeam(3, 4, 1, 240, 60));
        $this->addLeftSystem(new Thruster(4, 13, 0, 4, 3));

        $this->addRightSystem(new ParticleCannon(4, 8, 7, 0, 120));
        $this->addRightSystem(new ParticleCannon(4, 8, 7, 0, 120));
        $this->addRightSystem(new StdParticleBeam(3, 4, 1, 300, 120));
        $this->addRightSystem(new Thruster(4, 13, 0, 4, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(5, 32));
        $this->addLeftSystem(new Structure(4, 40));
        $this->addRightSystem(new Structure(4, 40));
    
            $this->hitChart = array(
        		0=> array(
        				8 => "Structure",
        				11 => "2:Thruster",
					12 => "1:Particle Repeater",
        				14 => "Scanner",
        				16 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		3=> array(
        				3 => "Thruster",
        				7 => "Particle Cannon",
        				9 => "Standard Particle Beam",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				3 => "Thruster",
        				7 => "Particle Cannon",
        				9 => "Standard Particle Beam",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    
    }
}
?>
