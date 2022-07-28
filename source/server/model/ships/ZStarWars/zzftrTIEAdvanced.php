<?php
class zzftrtieadvanced extends FighterFlight{
    /*StarWars TIE Advanced MkI...*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 45*6;
        $this->faction = "ZStarWars";
        $this->phpclass = "zzftrtieadv";
        $this->shipClass = "TIE Advanced Mk I";
        $this->imagePath = "img/starwars/tieAdvanced.png";
        
		$this->isd = "0 BBY";
		$this->notes = "Primary users: Galactic Empire.";
		$this->notes .= "<br>Hyperdrive";
	
	$this->limited = 10;	    
        $this->unofficial = true;
        
        $this->forwardDefense = 6;
        $this->sideDefense = 7;
        $this->freethrust = 12;
        $this->offensivebonus = 5; //revolutionary targeting computer
        $this->jinkinglimit = 6; //stacked with armor and weapons
        $this->turncost = 0.33; //stacked with armor and weapons
        
        $this->pivotcost = 2; //SW fighters have higher pivot cost - only elite pilots perform such maneuvers on screen!
		$this->enhancementOptionsEnabled[] = 'ELITE_SW'; //this flight can have Elite Pilot (SW) enhancement option	
        
    	$this->iniativebonus = 17 *5; //really heavy but with superior electronics
        
		
		$this->hangarRequired = "Fighter Squadrons"; //SW small craft are handled on squadron basis
		$this->unitSize = 6; //should be 9, but this is still experimental craft... 6 is deliberate
		
        $this->populate();
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(2, 2, 2, 2);
            $fighter = new Fighter("zzftrtieadv", $armour, 12, $this->id);
            $fighter->displayName = "TIE Advanced";
            $fighter->imagePath = "img/starwars/tieAdvanced.png";
            $fighter->iconPath = "img/starwars/tieAdvanced_large.png"; 
            
            $frontGun = new SWFighterLaser(330, 30, 3, 2); //front Lasers
            $fighter->addFrontSystem($frontGun);

           
            	//2 forward Concussion Missile Launchers, 3 shots each
            	$ConcussionMissileLauncher = new SWFtrConcMissileLauncher(3, 330, 30, 2);//single dual launcher! like for direct fire
            	$fighter->addFrontSystem($ConcussionMissileLauncher);

            
            //Ray Shield, 1 points
            $fighter->addAftSystem(new SWRayShield(0, 1, 0, 1, 0, 360));
            
            
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            
            $this->addSystem($fighter);
       }
    }
    
    
}
?>