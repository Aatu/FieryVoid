<?php
class AlacanRotiaFlight extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 78; //for 6
        $this->faction = "Small Races";
        $this->phpclass = "AlacanRotiaFlight";
        $this->shipClass = "Alacan Rotia flight";
        $this->imagePath = "img/ships/RogolonChelek.png";
        $this->forwardDefense = 6;
        $this->sideDefense = 6;
        $this->freethrust = 8;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 10;
        $this->turncost = 0.33;
        
       	$this->iniativebonus = 100;
        $this->populate();        
    }
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){   
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter("AlacanRotiaFlight", $armour, 6, $this->id);
            $fighter->displayName = "Rotia";
            $fighter->imagePath = "img/ships/RogolonChelek.png";
            $fighter->iconPath = "img/ships/RogolonChelek_Large.png";
            $frontGun = new LightFusionCannon(330, 30, 2, 1);
            $frontGun->displayName = "Light Particle Gun";
            $fighter->addFrontSystem($frontGun);
            $this->addSystem($fighter);
        }
    }
}
?>
