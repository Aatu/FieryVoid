<?php
class zzftrTIEBomber extends FighterFlight{
    /*StarWars TIE Bomber...*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 25*6;
        $this->faction = "StarWars Galactic Empire";
        $this->phpclass = "zzftrtiebomber";
        $this->shipClass =  "TIE Bombers";
        $this->imagePath = "img/starwars/tieBomber.png";
        
        //$this->isd = 2214;
        $this->unofficial = true;
        
        $this->forwardDefense = 7;
        $this->sideDefense = 7;
        $this->freethrust = 10;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
        
    	$this->iniativebonus = 18 *5; 
        
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
