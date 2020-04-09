<?php
class zzftrxwing extends FighterFlight{
    /*StarWars X-Wing...*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 48*6;
        $this->faction = "ZStarWars";
        $this->phpclass = "zzftrxwing";
        $this->shipClass = "X-Wing Superiority Fighters";
        $this->imagePath = "img/starwars/xWing.png";
        
		$this->isd = "early Galactic Civil War";
		$this->notes = "Primary users: Rebel Alliance, New Republic.";
		$this->notes .= "<br>Hyperdrive";
	    
        $this->unofficial = true;
        
        $this->forwardDefense = 7;
        $this->sideDefense = 8;
        $this->freethrust = 12;
        $this->offensivebonus = 4;
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
            $armour = array(2, 2, 2, 2);
            $fighter = new Fighter("zzftrxwing", $armour, 12, $this->id);
            $fighter->displayName = "X-Wing";
            $fighter->imagePath = "img/starwars/xWing.png";
            $fighter->iconPath = "img/starwars/xWing_large.png"; 
            
            $frontGun = new SWFighterLaser(330, 30, 2, 4); //front Lasers
            $fighter->addFrontSystem($frontGun);
            
           
            //2 forward Proton Torpedo Launchers, 3 shots each
            $torpedoLauncher = new SWFtrProtonTorpedoLauncher(3, 330, 30, 2);//single dual launcher! like for direct fire
            $fighter->addFrontSystem($torpedoLauncher);
            /*$torpedoLauncher = new SWFtrProtonTorpedoLauncher(3, 330, 30);
            $fighter->addFrontSystem($torpedoLauncher);*/
            
            
            
            //Ray Shield, 1 points
            $fighter->addAftSystem(new SWRayShield(0, 1, 0, 1, 0, 360));
            
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            
            
            $this->addSystem($fighter);
       }
    }
    
    
}
?>
