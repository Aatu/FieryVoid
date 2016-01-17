<?php
class wlcChlonasVesTekFlight extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 420; //70*6
        $this->faction = "Custom Ships";
        $this->phpclass = "wlcChlonasVesTekFlight";
        $this->shipClass = "Ch'Lonas Ves'Tek Assault Fighters";
        $this->imagePath = "img/ships/pikitos.png";

        $this->forwardDefense = 8;
        $this->sideDefense = 10;
        $this->freethrust = 10;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
        $this->iniativebonus = 85;
        $this->limited = 33;

        
        $this->populate();
    }

    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(3, 3, 3, 3);
            $fighter = new Fighter("wlcChlonasVesTekFlight", $armour, 13, $this->id);
            $fighter->displayName = "Ves'Tek Assault Fighter";
            $fighter->imagePath = "img/ships/pikitos.png";
            $fighter->iconPath = "img/ships/pikitos_large.png";
            $fighter->addFrontSystem(new PairedParticleGun(330, 30, 3));
            $fighter->addFrontSystem(new CustomLightMatterCannonF(345, 15));
            $this->addSystem($fighter);
        }
    }
}
?>