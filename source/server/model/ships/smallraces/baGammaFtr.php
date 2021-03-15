<?php
class baGammaFtr extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 42;
	$this->faction = "Small Races";
        $this->phpclass = "baGammaFtr";
        $this->shipClass = "BA Gamma Light Fighters";
	$this->imagePath = "img/ships/BAGamma.png";
	    $this->isd = 2135;
 		$this->unofficial = true;
        
        $this->forwardDefense = 6;
        $this->sideDefense = 6;
        $this->freethrust = 7;
        $this->offensivebonus = 2;
        $this->jinkinglimit = 10;
        $this->turncost = 0.33;
        
	$this->iniativebonus = 90;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
		$armour = array(1, 1, 1, 1);
		$fighter = new Fighter("baGammaFtr", $armour, 7, $this->id);
		$fighter->displayName = "Gamma";
		$fighter->imagePath = "img/ships/BAGamma.png";
		$fighter->iconPath = "img/ships/BAGamma_large.png";
			
        $frontGun = new GatlingGunFtr(330, 30, 0);
        $fighter->addFrontSystem($frontGun);
			
		$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
		
		$this->addSystem($fighter);
	}
    }
}


?>
