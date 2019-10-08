<?php
/*Brixadii Police Frigate is a puny ship - to reduce hassle, here's the option to field it as a flight of superheavy fighters (instead of single LCVs)*/
class BrixadiiScreenFrigateSHF extends FighterFlight{
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 70*6;
        $this->faction = "ZNexus Brixadii";
        $this->phpclass = "BrixadiiScreenFrigateSHF";
        $this->shipClass = "Screen Frigate squadron";
	    $this->variantOf = "Police Frigate squadron"; 
	    $this->occurence = "uncommon";
        $this->imagePath = "img/ships//Nexus/BrixadiiPoliceFrigateSHF.png";
        
        $this->isd = 2107;
        $this->unofficial = true;
        
        $this->forwardDefense = 9;
        $this->sideDefense = 11;
        $this->freethrust = 8;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 4; //deliberately low jinking limit and Init, they're SHFs grouped for convenience!
        $this->turncost = 0.33;
        $this->turndelaycost = 0.25;
        
		$this->hangarRequired = 'LCVs'; //for fleet check
    //these are patrol LCVs and don't really require hangars...
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
            $armour = array(3, 2, 2, 2);
            $fighter = new Fighter("BrixadiiScreenFrigateSHF", $armour, 24, $this->id);
            $fighter->displayName = "Screen Frigate";
            $fighter->imagePath = "img/ships/Nexus/BrixadiiPoliceFrigateSHF.png";
            $fighter->iconPath = "img/ships/Nexus/BrixadiiPoliceFrigateSHF_large.png"; 
            
            $weapon = new NexusParticleProjectorLight(0, 0, 0, 270, 30); //weapon designed for ship but should work all right
            $fighter->addFrontSystem($weapon);		
            $weapon = new NexusParticleProjectorLight(0, 0, 0, 300, 60); //weapon designed for ship but should work all right
            $fighter->addFrontSystem($weapon);		
            $weapon = new NexusParticleProjectorLight(0, 0, 0, 330, 90); //weapon designed for ship but should work all right
            $fighter->addFrontSystem($weapon);		
			
        	  $this->addSystem($fighter);
       }
    }
        
}
?>
