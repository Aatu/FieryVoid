<?php
class Wimuk extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 144;
	$this->faction = "Abbai (WotCR)";
        $this->phpclass = "Wimuk";
        $this->shipClass = "Wimuk Light Fighters";
    	$this->imagePath = "img/ships/AbbaiWimuk.png";

        $this->isd = 2009; 
       
        $this->forwardDefense = 6;
        $this->sideDefense = 6;
        $this->freethrust = 9;
        $this->offensivebonus = 4;
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
            $fighter = new Fighter("Wimuk", $armour, 6, $this->id);
            $fighter->displayName = "Wimuk";
            $fighter->imagePath = "img/ships/AbbaiWimuk.png";
            $fighter->iconPath = "img/ships/AbbaiWimuk_Large.png";
            $fighter->addFrontSystem(new PairedParticleGun(330, 30, 1));
  
	$this->addSystem($fighter);
        }
    }
}
?>
