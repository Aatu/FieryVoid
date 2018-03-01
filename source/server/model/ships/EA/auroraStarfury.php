<?php
class AuroraStarfury extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 342;
        $this->faction = "EA";
        $this->phpclass = "AuroraStarfury";
        $this->shipClass = "Starfury: Aurora Heavy flight";
        $this->imagePath = "img/ships/auroraStarfury.png";
        $this->isd = 2244;
        
	    $this->notes = 'Non-atmospheric.';
        
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
            $fighter = new Fighter("auroraStarfury", $armour, 13, $this->id);
            $fighter->displayName = "Aurora Heavy Fighter";
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
