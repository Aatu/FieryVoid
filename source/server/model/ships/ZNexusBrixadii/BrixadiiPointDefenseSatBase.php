<?php
class BrixadiiPointDefenseSatBase extends MicroSAT{
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 60*6;
        $this->faction = "ZNexus Brixadii";
        $this->phpclass = "BrixadiiPointDefenseSatBase";
        $this->shipClass = "Brixadii Point Defense Sat Cluster";
		$this->variantOf = "Brixadii Gun Sat Cluster";
		$this->occurence = "uncommon";
        $this->imagePath = "img/ships/Nexus/brixadii_temp_gunsat_model.png";
		$this->unofficial = true;        
		$this->isd = 2046;
	    
        
        $this->forwardDefense = 5;
        $this->sideDefense = 5;
        $this->freethrust = 4;
        $this->offensivebonus = 5; 
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
            $armour = array(2, 2, 2, 2);
            $fighter = new Fighter("Point Defense Sat", $armour, 28, $this->id);
            $fighter->displayName = "Point Defense Sat";
            $fighter->imagePath = "img/ships/Nexus/brixadii_temp_gunsat_model.png";
            $fighter->iconPath = "img/ships/Nexus/brixadii_temp_gunsat_model_large.png"; 
			
            $lightGun = new LightParticleProjector(0, 1, 0, 270, 90); 
			$lightGun->isLinked = false;
			$lightGun->shots = 1;
			$lightGun->defaultShots = 1;
			$lightGun->displayName = 'Light Particle Projector';		
			$lightGun->fireControl = array(0, 0, 0); // fighters, <mediums, <capitals	
			
			$lightGun2 = new LightParticleProjector(0, 1, 0, 270, 90); 
			$lightGun2->isLinked = false;
			$lightGun2->shots = 1;
			$lightGun2->defaultShots = 1;
			$lightGun2->displayName = 'Light Particle Projector';		
			$lightGun2->fireControl = array(0, 0, 0); // fighters, <mediums, <capitals	
			
			$lightGun3 = new LightParticleProjector(0, 1, 0, 270, 90); 
			$lightGun3->isLinked = false;
			$lightGun3->shots = 1;
			$lightGun3->defaultShots = 1;
			$lightGun3->displayName = 'Light Particle Projector';		
			$lightGun3->fireControl = array(0, 0, 0); // fighters, <mediums, <capitals	
			
            $fighter->addFrontSystem($lightGun);
            $fighter->addFrontSystem($lightGun2);
            $fighter->addFrontSystem($lightGun3);

        	$this->addSystem($fighter);
       }
    }
    
    
}
?>
