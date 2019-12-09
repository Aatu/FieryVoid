<?php
class Dovarum extends MicroSAT{
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 100*6;
        $this->faction = "Usuuth";
        $this->phpclass = "Dovarum";
        $this->shipClass = "Dovarum MicroSAT Cluster";
        $this->imagePath = "img/ships/UsuuthDovarum.png";
        
		$this->isd = 1950;
	    
        
        $this->forwardDefense = 9;
        $this->sideDefense = 8;
        $this->freethrust = 4;
        $this->offensivebonus = 4; 
        $this->turncost = 0.33; //actually not all that relevant...
        
		$this->hangarRequired = ""; //they don't require any hangars... although of course cannot be used in pickup battle either!
		$this->unitSize = 3; //number of craft in squadron
		
    	$this->iniativebonus = 15 *5; 
    	$this->superheavy = true;
        $this->maxFlightSize = 3;//this is a superheavy fighter originally intended as single unit, limit flight size to 3
		
		
        $this->populate();
		
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(3, 3, 3, 3);
            $fighter = new Fighter("Dovarum", $armour, 30, $this->id);
            $fighter->displayName = "Dovarum";
            $fighter->imagePath = "img/ships/UsuuthDovarum.png";
            $fighter->iconPath = "img/ships/UsuuthDovarum_large.png"; 
		            
			$hvyGun = new HvyParticleProjector(0, 1, 0, 330, 30); 
			$hvyGun->fireControl = array(-4, 0, 0); // fighters, <mediums, <capitals	
			$fighter->addFrontSystem($hvyGun);
			
       		//original SCS has LightProjectors linked
            $lightGun = new ParticleProjector(0, 1, 0, 330, 30); 
			//two linked guns...
			$lightGun->isLinked = true;
			$lightGun->shots = 2;
			$lightGun->defaultShots = 2;
			$lightGun->displayName = 'Twin-linked Particle Projector';		
			$lightGun->fireControl = array(-2, 0, 0); // fighters, <mediums, <capitals	
            $fighter->addFrontSystem($lightGun);
            
			
        	$this->addSystem($fighter);
       }
    }
    
    
}
?>
