<?php
class Rutarian extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 450;
	$this->faction = "Centauri";
	$this->phpclass = "Rutarian";
	$this->shipClass = "Rutarian Strike Fighters";
	$this->imagePath = "img/ships/rutarian.png";
	$this->customFtr = true;

	$this->forwardDefense = 6;
	$this->sideDefense = 4;
	$this->freethrust = 10;
	$this->offensivebonus = 5;
	$this->jinkinglimit = 8;
	$this->turncost = 0.33;

	$this->isd = 2258;
	$this->notes = 'Needs specially outfitted hangars to handle.';	    

	$this->iniativebonus = 18 *5;
	$this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){

		$armour = array(4, 1, 3, 3);
		$fighter = new Fighter("rutarian", $armour, 11, $this->id);
		$fighter->displayName = "Rutarian";
		$fighter->imagePath = "img/ships/rutarian.png";
		$fighter->iconPath = "img/ships/rutarian_large.png";


		$fighter->addFrontSystem(new IonBolt(330, 30));
		$fighter->addFrontSystem(new PairedParticleGun(330, 30, 2));

		$fighter->addAftSystem(new Stealth(1,1,0));

		$this->addSystem($fighter);

	}


    }

}
