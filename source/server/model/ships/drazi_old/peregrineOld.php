<?php
class PeregrineOld extends BaseShipNoAft{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 475;
        $this->faction = "Drazi (WotCR)";
        $this->phpclass = "peregrineOld";
        $this->imagePath = "img/ships/vulture.png";
        $this->shipClass = "Peregrine Jump Ship (1938)";
        $this->fighters = array("light" => 12);
        $this->limited = 33;
        
        $this->variantOf = "Peregrine Jump Ship";
        $this->isd = 1938;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 10;
	    
        $this->addPrimarySystem(new Reactor(5, 18, 0, 2));
        $this->addPrimarySystem(new CnC(5, 15, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 4, 6));
        $this->addPrimarySystem(new Engine(4, 15, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(3, 13));
        $this->addPrimarySystem(new JumpEngine(4, 12, 3, 38));
        $this->addPrimarySystem(new Thruster(4, 12, 0, 4, 2));
		$this->addPrimarySystem(new Thruster(4, 12, 0, 4, 2));
	    
		$this->addFrontSystem(new StdParticleBeam(3, 4, 1, 240, 60));
        $this->addFrontSystem(new HeavyPlasma(3, 8, 5, 300, 60));
        $this->addFrontSystem(new StdParticleBeam(3, 4, 1, 300, 120));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        
        $this->addLeftSystem(new StdParticleBeam(3, 4, 1, 180, 360));
        $this->addLeftSystem(new HeavyPlasma(3, 8, 5, 300, 360));
        $this->addLeftSystem(new Thruster(4, 16, 0, 4, 3));
	    
        $this->addRightSystem(new StdParticleBeam(3, 4, 1, 0, 180));
        $this->addRightSystem(new HeavyPlasma(3, 8, 5, 0, 60));
        $this->addRightSystem(new Thruster(4, 16, 0, 4, 4));
	    
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(5, 44));
        $this->addLeftSystem(new Structure(4, 40));
        $this->addRightSystem(new Structure(4, 40));
        $this->addFrontSystem(new Structure(4, 40));
    
	    
	$this->hitChart = array(
        		0=> array(
        				8=> "Structure",
        				10=> "Thruster",
        				12=> "Scanner",
						14=> "Jump Engine",
        				16=> "Engine",
        				18=> "Hangar",
        				19=> "Reactor",
        				20=> "C&C",
        		),
        		1=> array(
        				5=> "Thruster",
        				8=> "Heavy Plasma Cannon",
        				9=> "Standard Particle Beam",
        				18=> "Structure",
        				20=> "Primary",
        		),
        		3=> array(
        				5=> "Thruster",
        				7=> "Standard Particle Beam",
        				9=> "Heavy Plasma Cannon",
        				18=> "Structure",
        				20=> "Primary",
        		),
        		4=> array(
        				5=> "Thruster",
        				7=> "Standard Particle Beam",
        				9=> "Heavy Plasma Cannon",
        				18=> "Structure",
        				20=> "Primary",
        		),
        );
    }
}
?>
