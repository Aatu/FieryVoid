<?php
class Fessa extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 275;
		$this->faction = "Balosian";
        $this->phpclass = "Fessa";
        $this->imagePath = "img/ships/Hassa.png";
        $this->shipClass = "Fessa Warship";
        $this->canvasSize = 200;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 17;
        $this->fighters = array("heavy" => 6); //fighters on external racks!
        $this->occurence = "common";
        //$this->variantOf = 'Hassa Freighter'; //freighter will be in Civilians directory
       	$this->isd = 2239;        
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 0;
        $this->pivotcost = 0;
        
		$this->iniativebonus = -2 *5;
         
        $this->addPrimarySystem(new Reactor(3, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 2, 4));
 	    $this->addPrimarySystem(new Engine(3, 6, 0, 6, 4));
        $this->addPrimarySystem(new Hangar(3, 4));
        $this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 360));
        $this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 360));  
        $this->addPrimarySystem(new IonCannon(3, 6, 4, 240, 0));
        $this->addPrimarySystem(new IonCannon(3, 6, 4, 0, 120));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 2, 3));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 2, 4));

        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new CnC(3, 5, 0, 0));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 0, 360));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 0, 360));

        $this->addAftSystem(new Thruster(2, 8, 0, 2, 2));
		$this->addAftSystem(new Thruster(2, 8, 0, 2, 2));
		$this->addAftSystem(new Thruster(2, 8, 0, 2, 2));
  
        $this->addPrimarySystem(new Structure(3, 53+6)); //6 for external racks
		
        $this->hitChart = array(
            0 => array(
                10 => "Thruster",
                13 => "Scanner",
                16 => "Engine",
                18 => "Hangar",
                20 => "Reactor",
            ),
            1 => array(
                4 => "Thruster",
                7 => "Standard Particle Beam", //only front ones, correct - as I think by comparing with Hassa!
                9 => "C&C",                
                11 => "0:Ion Cannon",
                17 => "Structure",
                20 => "Primary",
            ),
            2 => array(
                3 => "Thruster",
				9 => "0:Standard Particle Beam",
                15 => "Structure",
                20 => "Primary",
            ),
        );
    }
}
?>
