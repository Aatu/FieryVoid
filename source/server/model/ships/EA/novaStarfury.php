<?php
class NovaStarfury extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 312;
        $this->faction = "EA";
        $this->phpclass = "NovaStarfury";
        $this->shipClass = "Starfury: Nova Heavy flight";
        $this->imagePath = "img/ships/auroraStarfury.png";
	    $this->isd = 2230;
        
        $this->forwardDefense = 8;
        $this->sideDefense = 6;
        $this->freethrust = 10;
        $this->offensivebonus = 4;
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
            $armour = array(2, 2, 2, 2);
            $fighter = new Fighter("novaStarfury", $armour, 12, $this->id);
            $fighter->displayName = "Nova";
            $fighter->imagePath = "img/ships/auroraStarfury.png";
            $fighter->iconPath = "img/ships/auroraStarfury_largei.png";

            $frontGun = new PairedParticleGun(330, 30, 4);
            $frontGun->displayName = "Uni-Pulse Cannon";
            $fighter->addFrontSystem($frontGun);
		$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack	
            $this->addSystem($fighter);
	}
    }
}
?>
