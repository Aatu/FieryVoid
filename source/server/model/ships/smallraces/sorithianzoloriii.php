<?php
class SorithianZolorIII extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 270;
        $this->faction = "Small Races";
        $this->phpclass = "SorithianZolorIII";
        $this->shipClass = "Sorithian Zolor III Fighters";
        $this->imagePath = "img/ships/BAStarFox.png";
        $this->forwardDefense = 7;
        $this->sideDefense = 7;
        $this->freethrust = 9;
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
            $fighter = new Fighter("SorithianZolorIII", $armour, 7, $this->id);
            $fighter->displayName = "Zolor III";
            $fighter->imagePath = "img/ships/BAStarFox.png";
            $fighter->iconPath = "img/ships/BAStarFox_large.png";

            $frontGun = new LightParticleBeam(330, 30, 1, 4);
            $frontGun->displayName = "Ultralight Particle Beam";
            $fighter->addFrontSystem($frontGun);
            $this->addSystem($fighter);
        }
    }
}
?>
