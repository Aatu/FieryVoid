<?php
class DudromaB_early extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 175;
		$this->faction = "Drazi Freehold (WotCR)";
        $this->phpclass = "DudromaB_early";
        $this->imagePath = "img/ships/dudroma.png";
        $this->shipClass = 'Dudroma B Defense Satellite';
	    $this->variantOf = 'Dudroma A Defense Satellite';
	    $this->isd = 1998;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 11;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;


        $this->addPrimarySystem(new Reactor(4, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 6, 3, 5)); 
        $this->addAftSystem(new Thruster(3, 4, 0, 0, 2)); 

        $this->addFrontSystem(new ParticleCannon(3, 8, 7, 300, 60));
        $this->addFrontSystem(new ParticleCannon(3, 8, 7, 300, 60));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 180, 360));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(4, 28));
	    
	    
	    
		$this->hitChart = array(
			0=> array(
					9 => "Structure",
					11 => "2:Thruster",
					14 => "1:Particle Cannon",
					16 => "1:Standard Particle Beam",
					18 => "Scanner",
					20 => "Reactor",
			)
		);
	    
    }
}
