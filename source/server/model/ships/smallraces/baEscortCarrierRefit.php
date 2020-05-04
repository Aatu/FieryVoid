<?php
class BAEscortCarrierRefit extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 320;
        $this->faction = "Small Races";
        $this->phpclass = "BAEscortCarrierRefit";
        $this->imagePath = "img/ships/BAEscortCarrier.png";
        $this->shipClass = "BA Escort Carrier (refit)";
        $this->fighters = array("normal"=>12, "heavy"=>12); //"heavy" are external racks

        $this->occurence = "uncommon";
        $this->variantOf = 'BA Escort Carrier';
        $this->isd = 2251;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
      
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;
        
        $this->addPrimarySystem(new Reactor(4, 18, 0, 0));
        $this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 4, 6));
        $this->addPrimarySystem(new Engine(4, 18, 0, 9, 2));
        $this->addPrimarySystem(new Thruster(4, 15, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(4, 15, 0, 5, 4));
	$this->addPrimarySystem(new Hangar(4, 14));
        
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
	$this->addFrontSystem(new LightPulse(3, 4, 2, 240, 60));
	$this->addFrontSystem(new LightPulse(3, 4, 2, 300, 120));
        $this->addFrontSystem(new BAInterceptorMkI(3, 4, 1, 270, 90));    
	$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 240, 60));
	$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 300, 120)); 

        $this->addAftSystem(new Thruster(4, 10, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 3, 2));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 120, 360));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 0, 240));
        $this->addAftSystem(new BAInterceptorMkI(3, 4, 1, 90, 270));  
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 36));
        $this->addPrimarySystem(new Structure( 4, 45));
        $this->addAftSystem(new Structure( 4, 36));
        
        $this->hitChart = array(
        	0=> array(
        		5 => "Thruster",
        		11 => "Structure",
        		13 => "Engine",
        		15 => "Scanner",
        		17 => "Hangar",
        		19 => "Reactor",
        		20 => "C&C",	
        	),
        	1=> array(
        		6 => "Thruster",
        		8 => "Light Pulse Cannon",
        		10 => "Standard Particle Beam",
        		11 => "BA Interceptor I",
        		18 => "Structure",
        		20 => "Primary",        			
        	),
        	2=> array(
        		6 => "Thruster",
        		8 => "Standard Particle Beam",
        		9 => "BA Interceptor I",
        		18 => "Structure",
        		20 => "Primary",           			
        	),
        );
    }
}
?>
