<?php
class zzftrAassaultTransport extends FighterFlight{
    /*StarWars Stormtrooper Transport...*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 47*6;
        $this->faction = "ZStarWars";
        $this->phpclass = "zzftrAassaultTransport";
        $this->shipClass = "ATR-6 Assault Transports";
        $this->imagePath = "img/starwars/AssaultTransport.png";
        
		$this->isd = "Galactic Civil War";
		$this->notes = "Primary users: Galactic Empire.";
	    
        $this->unofficial = true;
        
        $this->forwardDefense = 9;
        $this->sideDefense = 9;
        $this->freethrust = 8;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 0;
        $this->pivotcost = 3; //shuttles have pivot cost higher
        $this->turncost = 0.33;
        
    	$this->iniativebonus = 9 *5; 
        
		
		$this->hangarRequired = "Assault Squadrons"; //SW small craft are handled on squadron basis
		$this->unitSize = 6; //number of craft in squadron
        $this->maxFlightSize = 6; 
        
        $this->populate();
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(3, 2, 2, 2);
            $fighter = new Fighter("zzftrAassaultTransport", $armour, 15, $this->id);
            $fighter->displayName = "Assault Transport";
            $fighter->imagePath = "img/starwars/AssaultTransport.png";
            $fighter->iconPath = "img/starwars/AssaultTransport_Large.png"; 
            
            $frontGun = new SWFighterLaser(330, 30, 2, 4); //front Lasers
            $fighter->addFrontSystem($frontGun);
            $frontGun = new SWFighterIon(330, 30, 1, 2); //front ion cannons
            $frontGun->exclusive = true; //either this or other weapons! no separate gunner here!
            $fighter->addFrontSystem($frontGun);
                  
            //Ray Shield, 2 points
            $fighter->addAftSystem(new SWRayShield(0, 1, 0, 2, 0, 360));
            
            
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            
            $this->addSystem($fighter);
       }
    }
    
    
}
?>
