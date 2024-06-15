<?php
class LegionA extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 175;
        $this->faction = "Centauri Republic (WotCR)";
        $this->phpclass = "LegionA";
        $this->imagePath = "img/ships/legion.png";
        $this->shipClass = 'Legion A Satellite';
	    $this->isd = 1966;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));
        $this->addPrimarySystem(new Reactor(4, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 6, 3, 6)); 
        $this->addAftSystem(new Thruster(3, 4, 0, 0, 2)); 

        $this->addFrontSystem(new TacLaser(3, 5, 4, 270, 90));
        $this->addFrontSystem(new TacLaser(3, 5, 4, 270, 90));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(4, 24));
	    
	//d20 hit chart
	$this->hitChart = array(		
		0=> array(
			9 => "Structure",
			11 => "2:Thruster",
			14 => "1:Tactical Laser",
			16 => "1:Light Particle Beam",
			18 => "Scanner",
			30 => "Reactor",
		),
	);	    
    }
}
