<?php
class breachingpodabbai extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 40*6;
        $this->faction = "Abbai Matriarchate";
        $this->phpclass = "breachingpodabbai";
        $this->shipClass = "Kaltika Breaching Pod";
    	$this->imagePath = "img/ships/abbaibreachingpod.png";

        $this->isd = 2230; 
       
        $this->forwardDefense = 8;
        $this->sideDefense = 8;
        $this->freethrust = 6;
        $this->offensivebonus = 0;
        $this->jinkinglimit = 0;
        $this->turncost = 0.33;

        $this->maxFlightSize = 6;//this is an unusual type of 'fighter', limit flight size.      
		$this->hangarRequired = 'assault shuttles'; //for fleet check
		$this->unitSize = 1;        
        
        $this->iniativebonus = 90;
        $this->populate();
    }
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(4, 4, 4, 4); 
            $fighter = new Fighter("breachingpodabbai", $armour, 15, $this->id);
            $fighter->displayName = "Kaltika";
            $fighter->imagePath = "img/ships/abbaibreachingpod.png";
            $fighter->iconPath = "img/ships/abbaibreachingpod_Large.png";
            
			$fighter->addFrontSystem(new Marines(330, 30, 0, false)); //startarc, endarc, damagebonus, elite.

       	    //Grav Shield Level 1
            $fighter->addAftSystem(new FtrShield(1, 0, 360));
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
            $this->addSystem($fighter);
        }
    }
}
?>
