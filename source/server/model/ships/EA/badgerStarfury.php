<?php
class BadgerStarfury extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 420;
        $this->faction = "EA";
        $this->phpclass = "BadgerStarfury";
        $this->shipClass = "Starfury: Badger Heavy flight";
        $this->imagePath = "img/ships/badgerStarfury.png";

        $this->forwardDefense = 9;
        $this->sideDefense = 6;
        $this->freethrust = 10;
        $this->offensivebonus = 6;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;

        $this->iniativebonus = 80;
        $this->hasNavigator = true;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(3, 3, 2, 2);
            $fighter = new Fighter("badgerStarfury", $armour, 15, $this->id);
            $fighter->displayName = "Badger Starfury Heavy Fighter";
            $fighter->imagePath = "img/ships/badgerStarfury.png";
            $fighter->iconPath = "img/ships/badgerStarfury_large.png";

            $frontGun = new PairedParticleGun(330, 30, 4);
            $frontGun->displayName = "Uni-Pulse Cannon";
            $aftGun = new PairedParticleGun(150, 210, 4, 1);
            $aftGun->displayName = "Uni-Pulse Cannon";

            $fighter->addFrontSystem(new FighterMissileRack(4, 330, 30));
            $fighter->addFrontSystem($frontGun);
            $fighter->addFrontSystem(new FighterMissileRack(4, 330, 30));
            
            $fighter->addAftSystem($aftGun);
            
            $this->addSystem($fighter);
	   }
    }
}

?>