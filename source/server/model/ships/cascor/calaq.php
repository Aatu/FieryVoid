<?php
class Calaq extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 360;
        $this->faction = "Cascor";
        $this->phpclass = "Calaq";
        $this->shipClass = "Calaq Assault Fighters";
        $this->imagePath = "img/ships/CascorCalaq.png";
        $this->isd = 2224;
	    $this->notes = 'Non-atmospheric.';
        
        $this->forwardDefense = 9;
        $this->sideDefense = 8;
        $this->freethrust = 10;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 6;
        $this->accelcost = 2;
        $this->turncost = 0.33;
        
    	$this->iniativebonus = 16*5;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(2, 2, 2, 2);
            $fighter = new Fighter("Calaq", $armour, 10, $this->id);
            $fighter->displayName = "Calaq";
            $fighter->imagePath = "img/ships/CascorCalaq.png";
            $fighter->iconPath = "img/ships/CascorCalaq_Large.png";

            $frontGun = new Ionizer(330, 30, 1);
            $frontGun->displayName = "Ionizer";
            $frontGun2 = new IonBolt(330, 30);
            $frontGun2->displayName = "Ion Bolt";
            $fighter->addFrontSystem($frontGun);
            $fighter->addFrontSystem($frontGun2);
            $this->addSystem($fighter);
       }
    }
}
?>
