<?php
class Claweagle extends MediumShipLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 300;
		$this->faction = "Drazi Freehold";
        $this->phpclass = "Claweagle";
        $this->imagePath = "img/ships/drazi/DraziStareagle.png";
        $this->shipClass = "Claweagle Frigate";
			$this->occurence = "rare";
			$this->variantOf = 'Stareagle Frigate';        
        $this->agile = true;
        $this->canvasSize = 100;
		$this->isd = 2230;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 11;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.25;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 1;
		$this->iniativebonus = 70;

   		$this->enhancementOptionsEnabled[] = 'ELT_MRN'; //To enable Elite Marines enhancement
		$this->enhancementOptionsEnabled[] = 'EXT_MRN'; //To enable extra Marines enhancement


        $this->addPrimarySystem(new Reactor(5, 10, 0, 0));
        $this->addPrimarySystem(new CnC(5, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 8, 3, 5));
        $this->addPrimarySystem(new Engine(5, 13, 0, 8, 2));
		$this->addPrimarySystem(new Hangar(4, 1));
		$this->addPrimarySystem(new Quarters(4, 12));
		$this->addAftSystem(new Thruster(4, 10, 0, 4, 1));
		$this->addAftSystem(new Thruster(5, 14, 0, 7, 2));
		
        $this->addLeftSystem(new Thruster(4, 11, 0, 4, 3));
        $this->addLeftSystem(new GrapplingClaw(5, 0, 0, 300, 60, 8, false));
        $this->addLeftSystem(new StdParticleBeam(4, 4, 1, 240, 60));
		
        $this->addRightSystem(new Thruster(4, 11, 0, 4, 4));
        $this->addRightSystem(new GrapplingClaw(5, 0, 0, 300, 60, 8, false));
        $this->addRightSystem(new StdParticleBeam(4, 4, 1, 300, 120));
		
        $this->addPrimarySystem(new Structure( 5, 36));
    
            $this->hitChart = array(
        		0=> array(
						8 => "2:Thruster",
        				10 => "Quarters",
						13 => "Scanner",
        				15 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		3=> array(
        				5 => "Thruster",
        				7 => "Standard Particle Beam",        				
        				9 => "Grappling Claw",
        				17=> "0:Structure",        				
        				20 => "Primary",
        		),
        		3=> array(
        				5 => "Thruster",
        				7 => "Standard Particle Beam",        				
        				9 => "Grappling Claw",
        				17=> "0:Structure",        				
        				20 => "Primary",
        		),
        );
    
    }

}

?>
