<?php
class RaptorFtr extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 54;
	$this->faction = "EA (early)";
        $this->phpclass = "RaptorFtr";
        $this->shipClass = "Raptor Aerospace Fighter";
		$this->imagePath = "img/ships/raptor.png";
	    $this->isd = 2130;
 		$this->unofficial = true;
        
        $this->forwardDefense = 5;
        $this->sideDefense = 5;
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
		$armour = array(1, 0, 1, 1);
		$fighter = new Fighter("RaptorFtr", $armour, 7, $this->id);
		$fighter->displayName = "Raptor";
		$fighter->imagePath = "img/ships/raptor.png";
		$fighter->iconPath = "img/ships/raptor_large.png";
			
        $frontGun = new GatlingGunFtr(330, 30, 0);
        $fighter->addFrontSystem($frontGun);
			
		$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
		
		$this->addSystem($fighter);
	}
    }
}


?>
