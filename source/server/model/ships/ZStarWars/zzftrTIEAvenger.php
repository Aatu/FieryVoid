<?php
class zzftrtieavenger extends FighterFlight{
    /*StarWars Tie Adv-II Avenger...*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 50*6;
        $this->faction = "ZStarWars";
        $this->phpclass = "zzftrtieaveng";
        $this->shipClass = "TIE Avenger";
        $this->imagePath = "img/starwars/tieAvenger.png";
        
		$this->isd = "3 ABY";
		$this->notes = "Primary users: Galactic Empire.";
		$this->notes .= "<br>Hyperdrive";
	
	$this->limited = 33;	    
        $this->unofficial = true;
        
        $this->forwardDefense = 6;
        $this->sideDefense = 7;
        $this->freethrust = 12;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 8;
        $this->turncost = 0.25;
        
        $this->pivotcost = 2; //SW fighters have higher pivot cost - only elite pilots perform such maneuvers on screen!
		$this->enhancementOptionsEnabled[] = 'ELITE_SW'; //this flight can have Elite Pilot (SW) enhancement option	
        
    	$this->iniativebonus = 18 *5; 
        
		
		$this->hangarRequired = "Fighter Squadrons"; //SW small craft are handled on squadron basis
		$this->unitSize = 9; //number of craft in squadron
		
        $this->populate();
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(2, 1, 2, 2);
            $fighter = new Fighter("zzftrtieaveng", $armour, 11, $this->id);
            $fighter->displayName = "TIE Avenger";
            $fighter->imagePath = "img/starwars/tieAvenger.png";
            $fighter->iconPath = "img/starwars/tieAvenger_large.png"; 
            
            $frontGun = new SWFighterLaser(330, 30, 2, 4); //front Lasers
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