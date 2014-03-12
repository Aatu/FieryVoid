<?php
class NovaStarfury extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 312;
        $this->faction = "EA";
        $this->phpclass = "NovaStarfury";
        $this->shipClass = "Starfury: Nova Heavy flight";
        $this->imagePath = "img/ships/auroraStarfury.png";
        
        $this->forwardDefense = 8;
        $this->sideDefense = 6;
        $this->freethrust = 10;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
        
	$this->iniativebonus = 80;
        
        for ($i = 0; $i<6; $i++){
            $armour = array(2, 2, 2, 2);
            $fighter = new Fighter("novaStarfury", $armour, 12, $this->id);
            $fighter->displayName = "Nova StarFury Heavy Fighter";
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