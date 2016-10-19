<?php
class zzftrywing extends FighterFlight{
    /*StarWars Y-Wing... once all systems are in place, at least!*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 60*6;
        $this->faction = "Centauri Galactic Empire";
        $this->phpclass = "zzftrywing";
        $this->shipClass = "Y-Wing Tech Demo flight";
        $this->imagePath = "img/ships/auroraStarfury.png";
        
        /*
        $this->forwardDefense = 8;
        $this->sideDefense = 6;
        $this->freethrust = 12;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
        
    	$this->iniativebonus = 80;
        */
        
        $this->forwardDefense = 8;
        $this->sideDefense = 10;
        $this->freethrust = 8;
        $this->offensivebonus = 6;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
        
        $this->hasNavigator = true;
    	$this->iniativebonus = 17 *5; //includes Navigator bonus
        
        $this->populate();
    }
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(2, 1, 2, 2);
            $fighter = new Fighter("zzftrywing", $armour, 15, $this->id);
            $fighter->displayName = "Y-Wing Technology Demonstrator";
            $fighter->imagePath = "img/ships/auroraStarfury.png";
            $fighter->iconPath = "img/ships/auroraStarfury_largei.png";
            
            $frontGun = new PairedParticleGun(330, 30, 4);
            $fighter->addFrontSystem($frontGun);
            
            
            $this->addSystem($fighter);
       }
    }
}
?>
