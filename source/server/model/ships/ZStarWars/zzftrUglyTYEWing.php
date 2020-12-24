<?php
class zzftrUglyTYEWing extends FighterFlight{
    /*StarWars Tie Fighter Cockpit and Y-Wing Engine Ugly ...*/
	/*some data match with Medium and some with Light fighter, that is intentional*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 18*6;
        $this->faction = "ZStarWars";
        $this->phpclass = "zzftrUglyTYEWing";
        $this->shipClass = "Uglies TYE-Wing Fighters";
        $this->imagePath = "img/starwars/tieuglyTYE.png";
        $this->variantOf = "Uglies TIE-X Fighters";
        
		$this->isd = "early Galactic Civil War";
		$this->notes = "Common (civilian/pirate).";
	    
        $this->unofficial = true;
        
        $this->forwardDefense = 5;
        $this->sideDefense = 7;
        $this->freethrust = 11;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
        
        $this->pivotcost = 2; //SW fighters have higher pivot cost - only elite pilots perform such maneuvers on screen!
		$this->enhancementOptionsEnabled[] = 'ELITE_SW'; //this flight can have Elite Pilot (SW) enhancement option	
        

    	$this->iniativebonus = 20 *5; 
        
		
		$this->hangarRequired = "Fighter Squadrons"; //SW small craft are handled on squadron basis
		$this->unitSize = 12; //number of craft in squadron
        $this->maxFlightSize = 12;//this is a light fighter essentially
		
        $this->populate();
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(0, 2, 2, 1);
            $fighter = new Fighter("zzftrUglyTYEWing", $armour, 6, $this->id);
            $fighter->displayName = "Ugly TYE-Wing";
            $fighter->imagePath = "img/starwars/tieuglyTYE.png";
            $fighter->iconPath = "img/starwars/tieuglyTYE_Large.png"; 
            
            $frontGun = new SWFighterLaser(330, 30, 1, 2); //front Lasers
            $fighter->addFrontSystem($frontGun);
           
		$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            
            
            $this->addSystem($fighter);
       }
    }
    
    
}
?>
