<?php
class genericMicroSat extends MicroSAT{
	/*completely unofficial, my own take on how commercially available show-era MicroSAT might look like (its arguable that such units would be fielded by corporations with spaceborne assets)*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 145*6;
        $this->faction = "Civilians";
        $this->phpclass = "genericMicroSat";
        $this->shipClass = "MicroSAT Cluster";
        $this->imagePath = "img/ships/UsuuthDovarum.png";
        
		$this->isd = 'varied';
		$this->notes = "Generic MicroSAT design";
		
		$this->unofficial = true;
	    
        
        $this->forwardDefense = 9;
        $this->sideDefense = 9;
        $this->freethrust = 4;
        $this->offensivebonus = 4;
        $this->turncost = 0.33; //actually not all that relevant...
        
		$this->hangarRequired = ""; //they don't require any hangars... although of course cannot be used in pickup battle either!
		$this->unitSize = 3; //number of craft in squadron
		
    	$this->iniativebonus = 14 *5; 
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
            $fighter = new Fighter("genericMicroSat", $armour, 34, $this->id);
            $fighter->displayName = "MicroSAT";
            $fighter->imagePath = "img/ships/UsuuthDovarum.png";
            $fighter->iconPath = "img/ships/UsuuthDovarum_large.png"; 
		            
			
            $lightGun = new StdParticleBeam(0, 1, 0, 330, 30); 
			$lightGun->fireControl = array(0, 0, 0); // fighters, <mediums, <capitals	
            $fighter->addFrontSystem($lightGun);		
					
			$hvyGun = new MediumLaser(0, 1, 0, 330, 30); 
			$hvyGun->fireControl = array(-7, 0, 0); // fighters, <mediums, <capitals	
			$fighter->addFrontSystem($hvyGun);
			
            $lightGun = new StdParticleBeam(0, 1, 0, 330, 30); 
			$lightGun->fireControl = array(0, 0, 0); // fighters, <mediums, <capitals	
            $fighter->addFrontSystem($lightGun);
            
			
        	$this->addSystem($fighter);
       }
    }
    
    
}
?>
