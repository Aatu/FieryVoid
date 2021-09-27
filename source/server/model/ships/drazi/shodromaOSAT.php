<?php
class ShodromaOSAT extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 300;
		$this->faction = "Drazi";
        $this->phpclass = "shodromaosat";
        $this->imagePath = "img/ships/drazi/DraziShodroma.png";
        $this->shipClass = 'Shodroma Armed Satellite';
	    $this->isd = 2226;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 65;
        $this->addPrimarySystem(new Reactor(4, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 7, 3, 6)); 
        $this->addPrimarySystem(new Thruster(3, 4, 0, 0, 2)); 
        $this->addPrimarySystem(new HvyParticleCannon(4, 12, 9, 300, 60));
        $this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 180, 360));
        $this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(4, 40));
	    
	    
		$this->hitChart = array(
			0=> array(
					9 => "Structure",
					11 => "Thruster",
					14 => "Heavy Particle Cannon",
          				16 => "Standard Particle Beam",
					18 => "Scanner",
					20 => "Reactor",
			)
		);
	    
    }
}
