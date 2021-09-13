<?php
class Swallow1940 extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 300;
        $this->faction = "Drazi (WotCR)";
        $this->phpclass = "Swallow";
        $this->imagePath = "img/ships/shrike.png";
        $this->shipClass = "Swallow Light Carrier(1940)";
			$this->occurence = 'uncommon'; 
			$this->variantOf = "Shrike Heavy Destroyer";
        $this->isd = 1940;
        $this->canvasSize = 160;
        
        $this->fighters = array("light" => 12);
        
        $this->forwardDefense = 13;
        $this->sideDefense = 13;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 30;

        $this->addPrimarySystem(new Reactor(5, 10, 0, 2));
        $this->addPrimarySystem(new CnC(5, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 3, 5));
        $this->addPrimarySystem(new Engine(4, 12, 0, 7, 3));
        $this->addPrimarySystem(new Hangar(3, 1));
        $this->addPrimarySystem(new StdParticleBeam(3, 4, 1, 240, 60));
        $this->addPrimarySystem(new StdParticleBeam(3, 4, 1, 300, 120));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 1));
        $this->addPrimarySystem(new Thruster(4, 16, 0, 7, 2));

        $this->addLeftSystem(new Hangar(3, 6));
        $this->addLeftSystem(new StdParticleBeam(2, 4, 1, 240, 360));
        $this->addLeftSystem(new Thruster(4, 11, 0, 3, 3));

        $this->addRightSystem(new Hangar(3, 6));
        $this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 120));
        $this->addRightSystem(new Thruster(4, 11, 0, 3, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(4, 24));
        $this->addLeftSystem(new Structure(4, 36));
        $this->addRightSystem(new Structure(4, 36));
                    
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
        				5 => "Thruster",
                    	7 => "Standard Particle Beam",
        				10 => "Hangar",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				5 => "Thruster",
                    	7 => "Standard Particle Beam",
        				10 => "Hangar",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    
    }
}
?>