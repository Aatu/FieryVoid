<?php
class Pelican extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 325;
        $this->faction = "Drazi (WotCR)";
        $this->phpclass = "Pelican";
        $this->imagePath = "img/ships/drazi/DraziPelican.png";
        $this->shipClass = "Pelican Military Freighter";
        $this->isd = 1941;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 20;

        $this->addPrimarySystem(new Reactor(5, 10, 0, 0));
        $this->addPrimarySystem(new CnC(5, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 3, 5));
        $this->addPrimarySystem(new Engine(4, 10, 0, 6, 3));
        $this->addPrimarySystem(new Hangar(3, 4));
        $this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 240, 60));
        $this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 300, 120));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 1));
        $this->addPrimarySystem(new Thruster(4, 16, 0, 6, 2));

        $this->addLeftSystem(new StdParticleBeam(2, 4, 1, 240, 360));
        $this->addLeftSystem(new Thruster(4, 11, 0, 3, 3));
        $this->addLeftSystem(new CargoBay(2, 24));
        $this->addLeftSystem(new CargoBay(2, 24));

        $this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 120));
        $this->addRightSystem(new Thruster(4, 11, 0, 3, 4));
        $this->addRightSystem(new CargoBay(2, 24));
        $this->addRightSystem(new CargoBay(2, 24));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(4, 30));
        $this->addLeftSystem(new Structure(4, 42));
        $this->addRightSystem(new Structure(4, 42));
        
        $this->hitChart = array(
        		0=> array(
        				8 => "Structure",
        				11 => "Thruster",
						12 => "Standard Particle Beam",
        				14 => "Scanner",
        				16 => "Engine",
        				18 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		3=> array(
        				6 => "Thruster",
              			8 => "Standard Particle Beam",
        				11 => "Cargo Bay",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				6 => "Thruster",
              			8 => "Standard Particle Beam",
        				11 => "Cargo Bay",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    
    }
}
?>