<?php
class NightowlUpgr extends BaseShipNoAft{
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        $this->pointCost = 600;
        $this->faction = "Drazi (WotCR)";
        $this->phpclass = "NightowlUpgr";
        $this->imagePath = "img/ships/nightowl.png";
        $this->shipClass = "Nightowl Upgraded Explorer";
        $this->fighters = array("normal" => 12);
        $this->isd = 2014;
        $this->limited = 10;

	$this->unofficial = true;
        $this->occurence = "uncommon";
	$this->variantOf = 'Nightowl Hyperspace Probe'; 
		$this->notes = 'ALTERNATE UNIVERSE - unit designed for "In ancient times" campaign';

        $this->forwardDefense = 18;
        $this->sideDefense = 18;
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 4;
        $this->pivotcost = 4;
        
        $this->addPrimarySystem(new Reactor(5, 15, 0, 4));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new ElintScanner(5, 20, 6, 9));
        $this->addPrimarySystem(new Engine(5, 15, 0, 8, 3));
        $this->addPrimarySystem(new JumpEngine(5, 15, 4, 34));
        $this->addPrimarySystem(new Hangar(3, 14));
        $this->addPrimarySystem(new Thruster(4, 12, 0, 4, 2));
        $this->addPrimarySystem(new Thruster(4, 12, 0, 4, 2));
        
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 240, 120));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 240, 120));
        $this->addFrontSystem(new Thruster(3, 9, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 9, 0, 4, 1));
        
        $this->addLeftSystem(new StdParticleBeam(2, 4, 1, 240, 60));
        $this->addLeftSystem(new SolarCannon(4, 7, 3, 240, 360));
        $this->addLeftSystem(new CargoBay(3, 20));
        $this->addLeftSystem(new Thruster(4, 16, 0, 4, 3));
        
        $this->addRightSystem(new StdParticleBeam(2, 4, 1, 300, 120));
        $this->addRightSystem(new SolarCannon(4, 7, 3, 0, 120));
        $this->addRightSystem(new CargoBay(3, 20));
        $this->addRightSystem(new Thruster(4, 16, 0, 4, 4));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 55));
        $this->addLeftSystem(new Structure( 4, 52));
        $this->addRightSystem(new Structure( 4, 52));
        $this->addPrimarySystem(new Structure( 5, 55));
	    
	    
        //d20 hit chart
        $this->hitChart = array(		
            0=> array(
                8 => "Structure",
                10 => "Thruster",
                11 => "Jump Engine",
                13 => "Elint Scanner",
                15 => "Engine",
                17 => "Hangar",
                19 => "Reactor",
                20 => "C&C",
            ),
            1=> array(
                6 => "Thruster",
                8 => "Standard Particle Beam",
                18 => "Structure",
                20 => "Primary",
            ),
            3=> array(
                5 => "Thruster",
                7 => "Standard Particle Beam",
                9 => "Solar Cannon",
                11 => "Cargo Bay",
                18 => "Structure",
                20 => "Primary",
            ),
            4=> array(
                5 => "Thruster",
                7 => "Standard Particle Beam",
                9 => "Solar Cannon",
                10 => "Cargo Bay",
                18 => "Structure",
                20 => "Primary",
            ),
        );
    }
}
?>
