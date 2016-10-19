<?php
class aurorax extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 342;
        $this->faction = "Centauri Galactic Empire";
        $this->phpclass = "aurorax";
        $this->shipClass = "Y-Wing Tech Demo flight";
        $this->imagePath = "img/ships/auroraStarfury.png";
        
        $this->forwardDefense = 8;
        $this->sideDefense = 6;
        $this->freethrust = 12;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
        
    	$this->iniativebonus = 80;
        $this->populate();
    }
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(3, 2, 2, 2);
            $fighter = new Fighter("aurorax", $armour, 13, $this->id);
            $fighter->displayName = "Y-Wing Technology Demonstrator";
            $fighter->imagePath = "img/ships/auroraStarfury.png";
            $fighter->iconPath = "img/ships/auroraStarfury_largei.png";
            $frontGun = new PairedParticleGun(330, 30, 4);
            $frontGun->displayName = "Uni-Pulse Cannon";
            $fighter->addFrontSystem($frontGun);
            $this->addSystem($fighter);
       }
    }
}
?>
