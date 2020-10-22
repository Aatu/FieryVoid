<?php
class zzftrUglyTIEX extends FighterFlight{
    /*StarWars X-Wing Cockpit and Tie Fighter Wings Ugly ...*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 22*6;
        $this->faction = "ZStarWars";
        $this->phpclass = "zzftrUglyTIEX";
        $this->shipClass = "Uglies TIE-X";
        $this->imagePath = "img/starwars/tieuglytiex.png";
        
		$this->isd = "early Galactic Civil War";
		$this->notes = "Common (civilian/pirate).";
	    
        $this->unofficial = true;
        
        $this->forwardDefense = 5;
        $this->sideDefense = 7;
        $this->freethrust = 7;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
        
        $this->pivotcost = 2; //SW fighters have higher pivot cost - only elite pilots perform such maneuvers on screen!
		$this->enhancementOptionsEnabled[] = 'ELITE_SW'; //this flight can have Elite Pilot (SW) enhancement option	
        

    	$this->iniativebonus = 16 *5; 
        
		
		$this->hangarRequired = "Fighter Squadrons"; //SW small craft are handled on squadron basis
		$this->unitSize = 9; //number of craft in squadron
		
        $this->populate();
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(2, 1, 1, 0);
            $fighter = new Fighter("zzftrUglyTIEX", $armour, 8, $this->id);
            $fighter->displayName = "Ugly TIE-X";
            $fighter->imagePath = "img/starwars/tieuglytiex.png";
            $fighter->iconPath = "img/starwars/tieuglytiex_large.png"; 
            
            $frontGun = new SWFighterLaser(330, 30, 2, 2); //front Lasers
            $fighter->addFrontSystem($frontGun);
           
            //Ray Shield, 1 points
            $fighter->addAftSystem(new SWRayShield(0, 1, 0, 1, 0, 360));
            
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            
            
            $this->addSystem($fighter);
       }
    }
    
    
}
?>