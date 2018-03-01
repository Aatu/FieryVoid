<?php
class Kotha extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 210;
        $this->faction = "Abbai";
        $this->phpclass = "Kotha";
        $this->shipClass = "Kotha Medium Fighters";
    	$this->imagePath = "img/ships/AbbaiKotha.png";

        $this->isd = 2230; 
       
        $this->forwardDefense = 7;
        $this->sideDefense = 7;
        $this->freethrust = 11;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
        $this->iniativebonus = 90;
        $this->populate();
    }
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(3, 3, 3, 3);
            $fighter = new Fighter("Kotha", $armour, 9, $this->id);
            $fighter->displayName = "Kotha";
            $fighter->imagePath = "img/ships/AbbaiKotha.png";
            $fighter->iconPath = "img/ships/AbbaiKotha_Large.png";
            $fighter->addFrontSystem(new PairedParticleGun(330, 30, 1));

       	    //Grav Shield Level 1
            $fighter->addAftSystem(new FtrShield(1, 0, 360));

            $this->addSystem($fighter);
        }
    }
}
?>
