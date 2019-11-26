<?php
class Caccar extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 198;
        $this->faction = "Cascor";
        $this->phpclass = "Caccar";
        $this->shipClass = "Caccar Ultralight Fighters";
        $this->imagePath = "img/ships/CascorCaccar.png";
        $this->isd = 2209;
	    $this->notes = 'Non-atmospheric.';
        
        $this->forwardDefense = 7;
        $this->sideDefense = 5;
        $this->freethrust = 18;
        $this->offensivebonus = 6;
        $this->jinkinglimit = 99;
        $this->accelcost = 2;
        $this->turncost = 0.33;
        
    	$this->iniativebonus = 100;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(1, 0, 0, 0);
            $fighter = new Fighter("Caccar", $armour, 5, $this->id);
            $fighter->displayName = "Caccar";
            $fighter->imagePath = "img/ships/CascorCaccar.png";
            $fighter->iconPath = "img/ships/CascorCaccar_Large.png";

            $frontGun = new Ionizer(330, 30, 2);
            $frontGun->displayName = "Ionizer";
            $fighter->addFrontSystem($frontGun);
            $this->addSystem($fighter);
       }
    }
}
?>
