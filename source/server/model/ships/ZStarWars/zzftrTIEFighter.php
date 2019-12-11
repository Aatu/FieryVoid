<?php
class zzftrTIEFighter extends FighterFlight{
    /*StarWars TIE Fighter...*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 24*6;
        $this->faction = "ZStarWars";
        $this->phpclass = "zzftrtiefighter";
        $this->shipClass =  "TIE Fighters";
        $this->imagePath = "img/starwars/tieFighter.png";
        
		$this->isd = "18 BBY";
		$this->notes = "Primary users: Galactic Empire.";
	    
        //$this->isd = 2214;
        $this->unofficial = true;
        
        $this->forwardDefense = 5;
        $this->sideDefense = 6;
        $this->freethrust = 11;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 10;
        $this->turncost = 0.25;
        
    	$this->iniativebonus = 20 *5; 
        
		
		$this->hangarRequired = "Fighter Squadrons"; //SW small craft are handled on squadron basis
		$this->unitSize = 12; //number of craft in squadron
		
        $this->populate();
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(0, 0, 1, 1);
            $fighter = new Fighter("zzftrtiefighter", $armour, 5, $this->id);
            $fighter->displayName = "TIE Fighter";
            $fighter->imagePath = "img/starwars/tieFighter.png";
            $fighter->iconPath = "img/starwars/tieFighter_large.png"; 
            
            $frontGun = new SWFighterLaser(330, 30, 1, 2); //front Lasers
            $fighter->addFrontSystem($frontGun);
            
           
            
            $this->addSystem($fighter);
       }
    }
    
    
}
?>
