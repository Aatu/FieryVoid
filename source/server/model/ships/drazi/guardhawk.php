<?php
class Guardhawk extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 550;
	$this->faction = "Drazi";
        $this->phpclass = "Guardhawk";
        $this->imagePath = "img/ships/drazi/DraziSunhawk.png";
        $this->shipClass = "Guardhawk Battle Escort";
        $this->occurence = "uncommon";
	    $this->variantOf = "Sunhawk Battlecruiser";
	    $this->isd = 2234;
        $this->canvasSize = 256;

        $this->forwardDefense = 14;
        $this->sideDefense = 13;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 40;

        $this->addPrimarySystem(new Reactor(5, 14, 0, 4));
        $this->addPrimarySystem(new CnC(5, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 13, 4, 8));
        $this->addPrimarySystem(new Engine(5, 11, 0, 10, 2));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new StdParticleBeam(4, 4, 1, 300, 60));
        $this->addPrimarySystem(new Thruster(4, 15, 0, 5, 1));
        $this->addPrimarySystem(new Thruster(5, 21, 0, 10, 2));

        $this->addLeftSystem(new ParticleRepeater(4, 6, 4, 240, 0));
        $this->addLeftSystem(new ParticleRepeater(4, 6, 4, 240, 0));
        $this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));
        $this->addLeftSystem(new StdParticleBeam(4, 4, 1, 240, 60));

        $this->addRightSystem(new ParticleRepeater(4, 6, 4, 0, 120));
        $this->addRightSystem(new ParticleRepeater(4, 6, 4, 0, 120));
        $this->addRightSystem(new Thruster(4, 15, 0, 5, 4));
        $this->addRightSystem(new StdParticleBeam(4, 4, 1, 300, 120));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(6, 36));
        $this->addLeftSystem(new Structure(5, 44));
        $this->addRightSystem(new Structure(5, 44));
    
                    $this->hitChart = array(
        		0=> array(
        				8 => "Structure",
        				11 => "Thruster",
					12 => "Standard Particle Beam",
        				14 => "Scanner",
        				16 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		3=> array(
        				3 => "Thruster",
        				7 => "Particle Repeater",
        				9 => "Standard Particle Beam",
					18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				3 => "Thruster",
        				7 => "Particle Repeater",
        				9 => "Standard Particle Beam",
					18 => "Structure",
        				20 => "Primary",
        		),
        );
    
    }
}
