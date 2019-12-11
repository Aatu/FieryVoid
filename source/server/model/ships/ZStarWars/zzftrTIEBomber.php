<?php
class zzftrTIEBomber extends FighterFlight{
    /*StarWars TIE Bomber... let's make it a Heavy fighter, it's very stocky...*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 26*6;
        $this->faction = "ZStarWars";
        $this->phpclass = "zzftrtiebomber";
        $this->shipClass =  "TIE Bombers";
        $this->imagePath = "img/starwars/tieBomber.png";

		$this->isd = "Galactic Civil War";
		$this->notes = "Primary users: Galactic Empire.";
	    
        $this->unofficial = true;
        
        $this->forwardDefense = 7;
        $this->sideDefense = 7;
        $this->freethrust = 10;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
        
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
            $armour = array(1, 1, 2, 2);
            $fighter = new Fighter("zzftrtiebomber", $armour, 10, $this->id);
            $fighter->displayName = "TIE Bomber";
            $fighter->imagePath = "img/starwars/tieBomber.png";
            $fighter->iconPath = "img/starwars/tieBomber_large.png"; 
            
            $frontGun = new SWFighterLaser(330, 30, 1, 2); //front Lasers
            $fighter->addFrontSystem($frontGun);
            
            //2 forward Proton Torpedo Launchers, 4 shots each
            $torpedoLauncher = new SWFtrProtonTorpedoLauncher(4, 330, 30, 2); //single dual launcher! like for direct fire
            $fighter->addFrontSystem($torpedoLauncher);
            /*
            $torpedoLauncher = new SWFtrProtonTorpedoLauncher(4, 330, 30);
            $fighter->addFrontSystem($torpedoLauncher);
            */
            $this->addSystem($fighter);
       }
    }
    
    
}
?>
