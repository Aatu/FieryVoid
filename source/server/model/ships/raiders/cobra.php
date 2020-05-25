<?php
class Cobra extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 30 *6;
	$this->faction = "Raiders";
	$this->phpclass = "Cobra";
        $this->shipClass = "Cobra Light Fighters";
	$this->imagePath = "img/ships/dragon.png"; 
        $this->limited = 33; //difficult to maintain for non-Drazi, hence limitation
        
        $this->forwardDefense = 6;
        $this->sideDefense = 7;
        $this->freethrust = 10;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 10;
        $this->turncost = 0.33;
        
	$this->iniativebonus = 20 *5; //Drazi design but not necessarily Drazi piloted, hence no Drazi Ini bonus
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
		$armour = array(1, 1, 1, 1);
		$fighter = new Fighter("Cobra", $armour, 5, $this->id);
		$fighter->displayName = "Cobra";
		$fighter->imagePath = "img/ships/dragon.png"; 
		$fighter->iconPath = "img/ships/dragon_large.png"; 
			
		$fighter->addFrontSystem(new PairedParticleGun(330, 30, 2));
			
		$this->addSystem($fighter);
	}
    }
}


