<?php
class tfsblockaderunner extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 320;
	$this->faction = "Raiders";
        $this->phpclass = "tfsblockaderunner";
        $this->imagePath = "img/ships/drazi/warbird.png";
        $this->shipClass = "TFS Blockade Runner";
	    $this->isd = 2249;
        $this->canvasSize = 256;
	    $this->unofficial = true;

		$this->notes = "Used only by the Tirrith Free State";
//		$this->notes .= "<br>May only use half its EW offensively";
		$this->notes .= "<br>Custom version of the official unit. This can only use half of its EW offensively. This is not an option in FV just yet. Therefore, this only has 6 EW until this feature is added.";

        $this->forwardDefense = 13;
        $this->sideDefense = 12;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;

        $this->addPrimarySystem(new Reactor(5, 10, 0, 0));
        $this->addPrimarySystem(new CnC(5, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 4, 6));
        $this->addPrimarySystem(new Engine(5, 20, 0, 12, 2));
        $this->addPrimarySystem(new Hangar(4, 1));
		$this->addPrimarySystem(new CargoBay(4, 32));
        $this->addPrimarySystem(new Thruster(4, 15, 0, 6, 1));
        $this->addPrimarySystem(new Thruster(5, 21, 0, 12, 2));

        $this->addLeftSystem(new StdParticleBeam(3, 4, 1, 240, 60));
        $this->addLeftSystem(new StdParticleBeam(3, 4, 1, 240, 60));
        $this->addLeftSystem(new Thruster(4, 15, 0, 6, 3));

        $this->addRightSystem(new StdParticleBeam(3, 4, 1, 300, 120));
        $this->addRightSystem(new StdParticleBeam(3, 4, 1, 300, 120));
        $this->addRightSystem(new Thruster(4, 15, 0, 6, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(5, 32));
        $this->addLeftSystem(new Structure(4, 40));
        $this->addRightSystem(new Structure(4, 40));
    
            $this->hitChart = array(
        		0=> array(
        				7 => "Structure",
        				10 => "Thruster",
						12 => "Cargo Bay",
        				14 => "Scanner",
        				16 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		3=> array(
        				5 => "Thruster",
        				8 => "Standard Particle Beam",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				5 => "Thruster",
        				8 => "Standard Particle Beam",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    
    }
}
?>
