<?php
class zzftrBWingPrototype extends FighterFlight{
    /*StarWars B-Wing Prototype*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 80*6;
        $this->faction = "ZStarWars";
        $this->phpclass = "zzftrBWingPrototype";
        $this->shipClass = "B-Wing Prototype Assault Fighters";
        $this->variantOf = "B-Wing Assault Fighters";
        $this->occurence = "rare";
        $this->limited = 33; //Limited Deployment
        $this->imagePath = "img/starwars/bWing.png";
        
		$this->isd = "2 ABY";
		$this->notes = "Primary users: Rebel Alliance.";
		$this->notes .= "<br>Hyperdrive";
	    

        $this->unofficial = true;
        
        $this->forwardDefense = 7;
        $this->sideDefense = 9;
        $this->freethrust = 8;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
        
    	$this->iniativebonus = 16 *5; //Navigator bonus is automatically added on top of that
        
        $this->pivotcost = 2; //SW fighters have higher pivot cost - only elite pilots perform such maneuvers on screen!
		$this->enhancementOptionsEnabled[] = 'ELITE_SW'; //this flight can have Elite Pilot (SW) enhancement option	
        
		
		$this->hangarRequired = "Fighter Squadrons"; //SW small craft are handled on squadron basis
		$this->unitSize = 6; //number of craft in squadron
		
        $this->populate();
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(3, 2, 3, 3);
            $fighter = new Fighter("zzftrBWingPrototype", $armour, 18, $this->id);
            $fighter->displayName = "B-Wing Prototype";
            $fighter->imagePath = "img/starwars/bWing.png";
            $fighter->iconPath = "img/starwars/bWing_large.png"; 
            
            $frontGun = new SWFighterLaser(330, 30, 4, 3); //fwd triple Laser Cannons
            $fighter->addFrontSystem($frontGun);
            
			
            $ASGun = new SWFighterASLaserR(330, 30, 3, 12); //damage of 3 dice (d6) + 12 flat - always single mount
            $ASGun->exclusive = true; //either this or other weapons! no separate gunner here!
            $fighter->addFrontSystem($ASGun);
			
			//prototype didn't have a launcher
            
            //Ray Shield, 2 points
            $fighter->addAftSystem(new SWRayShield(0, 1, 0, 2, 0, 360));

            
		$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            
            
            $this->addSystem($fighter);
       }
    }
    
    
}
?>
