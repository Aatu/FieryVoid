<?php
class SorithianZolorI extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 180;
        $this->faction = "Small Races";
        $this->phpclass = "SorithianZolorI";
        $this->shipClass = "Sorithian Zolor I Fighters";
        $this->imagePath = "img/ships/BAStarFox.png";
        $this->forwardDefense = 7;
        $this->sideDefense = 7;
        $this->freethrust = 12;
        $this->offensivebonus = 2;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
        
        $this->unofficial = true;
        
    	$this->iniativebonus = 90;
        $this->populate();        
    }
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){   
            $armour = array(1, 0, 1, 1);
            $fighter = new Fighter("SorithianZolorI", $armour, 7, $this->id);
            $fighter->displayName = "Zolor I";
            $fighter->imagePath = "img/ships/BAStarFox.png";
            $fighter->iconPath = "img/ships/BAStarFox_large.png";

            $frontGun = new LightParticleBeam(330, 30, 1, 2);
            $frontGun->displayName = "Ultralight Particle Beam";
            $fighter->addFrontSystem($frontGun);
            $this->addSystem($fighter);
        }
    }
}
?>
