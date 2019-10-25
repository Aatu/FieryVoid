<?php
class LegionB extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 125;
        $this->faction = "Centauri (WotCR)";
        $this->phpclass = "LegionB";
        $this->imagePath = "img/ships/legion.png";
        $this->shipClass = 'Legion B Satellite';
        $this->variantOf = 'Legion A Satellite';
	    $this->isd = 1966;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;


        $this->addPrimarySystem(new Reactor(4, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 6, 3, 6)); 
        $this->addPrimarySystem(new Thruster(3, 4, 0, 0, 2)); 

        $this->addPrimarySystem(new ImperialLaser(3, 8, 5, 270, 90));
        $this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        $this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(4, 24));
	    

	//d20 hit chart
	$this->hitChart = array(		
		0=> array(
			9 => "Structure",
			11 => "Thruster",
			14 => "Imperial Laser",
			16 => "Light Particle Beam",
			18 => "Scanner",
			30 => "Reactor",
		),
	);	    
	    
	    
    }
}
