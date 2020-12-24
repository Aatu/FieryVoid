<?php
class zzftrUglyChirdaki extends FighterFlight{
    /*StarWars Tie-Fighter Cockpit and gyroscopic mounted X-Wing foils ...*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 42*6;
        $this->faction = "ZStarWars";
        $this->phpclass = "zzftrUglyChirdaki";
        $this->shipClass = "Uglies Chirdaki Fighters";
        $this->variantOf = "Uglies TIE-X Fighters";
        $this->imagePath = "img/starwars/tieuglychirdaki.png";
        
		$this->isd = "early Galactic Civil War";
		$this->notes = "Common (civilian/pirate).";
		$this->notes .= "<br>Hyperdrive";
	    
        $this->unofficial = true;
        
        $this->forwardDefense = 7;
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
            $armour = array(2, 2, 2, 1);
            $fighter = new Fighter("zzftrUglyChirdaki", $armour, 11, $this->id);
            $fighter->displayName = "Ugly Chirdaki";
            $fighter->imagePath = "img/starwars/tieuglychirdaki.png";
            $fighter->iconPath = "img/starwars/tieuglychirdaki_Large.png"; 
            
            $frontGun = new SWFighterLaser(330, 30, 2, 4); //front Lasers
            $fighter->addFrontSystem($frontGun);
           
            //Ray Shield, 1 points
            $fighter->addAftSystem(new SWRayShield(0, 1, 0, 1, 0, 360));
            
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            
            
            $this->addSystem($fighter);
       }
    }
    
    
}
?>