<?php
class scionbp extends SuperHeavyFighter{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 40;
        $this->faction = "Centauri";
        $this->phpclass = "scionbp";
        $this->shipClass = "Scion Breaching Pod";
        $this->imagePath = "img/ships/skyserpent.png";
	    //$this->isd = 2220;

        $this->forwardDefense = 9;
        $this->sideDefense = 8;
        $this->freethrust = 9;
        $this->offensivebonus = 0; //irrelevant
        $this->jinkinglimit = 0;
        $this->turncost = 0.33;

        $this->iniativebonus = 9 *5;
        
        $armour = array(2, 2, 2, 2);
        $fighter = new Fighter("scionbp", $armour, 16, $this->id);
        $fighter->displayName = "Scion Breaching Pod";
        $fighter->imagePath = "img/ships/phalan.png";
        $fighter->iconPath = "img/ships/phalan_large.png";

	/* no weapons!
        $fighter->addFrontSystem(new PairedParticleGun(330, 30, 5));
        $fighter->addFrontSystem(new FighterMissileRack(6, 330, 30));
        $fighter->addFrontSystem(new FighterMissileRack(6, 330, 30));
        
        $particleBlaster = new ParticleBlaster(0, 0, 0, 330, 30);
        $particleBlaster->fireControl = array(-4, 0, 0);
        $particleBlaster->loadingtime = 3;
        $fighter->addFrontSystem($particleBlaster);
        */
        $this->addSystem($fighter);
    }

    public function populate(){
        return;
    }
}

?>
