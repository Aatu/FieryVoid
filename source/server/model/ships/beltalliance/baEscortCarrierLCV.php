<?php
class BAEscortCarrierLCV extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 350;
        $this->faction = "Belt Alliance";
        $this->phpclass = "BAEscortCarrierLCV";
        $this->imagePath = "img/ships/BAEscortCarrier2.png";
        $this->shipClass = "BA Escort LCV Carrier";
			$this->variantOf = "BA Escort Carrier";
        $this->occurence = "common";
        $this->fighters = array("LCVs"=>2, "heavy"=>12); //"heavy" are external racks

        $this->isd = 2235;
        
		$this->notes = "This ship is intended to allow Belters to field LCVs in pickup battle."; //by the fluff such a ship is not needed for them! as they don't project power beyond areas where LCVs can get on their own
	    $this->unofficial = 'S'; //Semi-official - added as reasonably close to official ship, while expanding Belters options in regards to fielding LCVs
		
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
		$this->addPrimarySystem(new Hangar(4, 2));
        $LCVRail = new DockingCollar(3, 8);
        $LCVRail->displayName = "LCV Rail";
        $this->addPrimarySystem($LCVRail);
        $LCVRail = new DockingCollar(3, 8);
        $LCVRail->displayName = "LCV Rail";
        $this->addPrimarySystem($LCVRail);
        
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
		$this->addFrontSystem(new MedBlastCannon(3, 0, 0, 240, 60));
		$this->addFrontSystem(new MedBlastCannon(3, 0, 0, 300, 120));
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
        		10 => "Structure",
        		12 => "Engine",
        		14 => "Scanner",
        		15 => "Hangar",
                17 => "LCV Rail",
        		19 => "Reactor",
        		20 => "C&C",	
        	),
        	1=> array(
        		6 => "Thruster",
        		8 => "Medium Blast Cannon",
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
