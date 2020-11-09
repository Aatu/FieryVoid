<?php
class zzftrUglyXTIE extends FighterFlight{
    /*StarWars X-Wing Cockpit and Tie Fighter Wings Ugly ...*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 18*6; //with torpedo launcher it's supposed to be 20*6;
        $this->faction = "ZStarWars";
        $this->phpclass = "zzftrUglyXTIE";
        $this->shipClass = "Uglies X-TIE Bombers";
        $this->variantOf = "Uglies TIE-X Fighters";
        $this->imagePath = "img/starwars/tieuglyxtie.png";
        
		$this->isd = "early Galactic Civil War";
		$this->notes = "Common (civilian/pirate).";
	    
        $this->unofficial = true;
        
        $this->forwardDefense = 9;
        $this->sideDefense = 8;
        $this->freethrust = 6;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
        
        $this->pivotcost = 2; //SW fighters have higher pivot cost - only elite pilots perform such maneuvers on screen!
		$this->enhancementOptionsEnabled[] = 'ELITE_SW'; //this flight can have Elite Pilot (SW) enhancement option	
 
        $this->hasNavigator = true;       

    	$this->iniativebonus = 14 *5; //Navigator bonus is automatically added on top of that
		//not a mistake, this fighter should have poorer Ini than usual
        
		
		$this->hangarRequired = "Fighter Squadrons"; //SW small craft are handled on squadron basis
		$this->unitSize = 9; //number of craft in squadron
		
        $this->populate();
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(1, 1, 1, 0);
            $fighter = new Fighter("zzftrUglyXTIE", $armour, 11, $this->id);
            $fighter->displayName = "Ugly X-TIE";
            $fighter->imagePath = "img/starwars/tieuglyxtie.png";
            $fighter->iconPath = "img/starwars/tieuglyxtie_Large.png"; 
            
            $frontGun = new SWFighterLaser(330, 30, 1, 2); //front Lasers
            $fighter->addFrontSystem($frontGun);
           
		/* as of now it is not possible to have fighter with two separate systems trying to buy ammo... disabling torpedo launcher as kind of fix
            $torpedoLauncher = new SWFtrProtonTorpedoLauncher(4, 330, 30, 1);//single launcher!
            $fighter->addFrontSystem($torpedoLauncher);
	    */

            //forward SINLGLE Concussion Missile Launcher, 4 shots
			$ConcussionMissileLauncher = new SWFtrConcMissileLauncher(4, 330, 30, 1);
			$fighter->addFrontSystem($ConcussionMissileLauncher);
            

            //Ray Shield, 1 points
            $fighter->addAftSystem(new SWRayShield(0, 1, 0, 1, 0, 360));
            
	    $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            
            
            $this->addSystem($fighter);
       }
    }
    
    
}
?>
