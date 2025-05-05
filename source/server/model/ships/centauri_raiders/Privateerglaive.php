<?php
class Privateerglaive extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 28*6;
        $this->faction = "Raiders";
        $this->phpclass = "Privateerglaive";
        $this->shipClass = "Centauri Privateer Glaive Light Fighters";
	$this->imagePath = "img/ships/RaiderGlaive.png";
	$this->isd = 1995;
	    
	$this->notes = "Since 2012 available to all Raiders.";
        
        $this->forwardDefense = 7;
        $this->sideDefense = 7;
        $this->freethrust = 12;
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
			$fighter = new Fighter("glaive", $armour, 8, $this->id);
			$fighter->displayName = "Glaive";
			$fighter->imagePath = "img/ships/RaiderGlaive.png";
			$fighter->iconPath = "img/ships/RaiderGlaive_large.png";
			
			
			$fighter->addFrontSystem(new PairedPlasmaBlaster(330, 30));
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			
			$this->addSystem($fighter);
			
		}
		
		
    }

}



?>
