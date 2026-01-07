<?php
class Rotas extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 270;
	$this->faction = "Centauri Republic";
	$this->phpclass = "Rotas";
	$this->shipClass = "Rotas Interceptor";
		$this->variantOf = "Rutarian Strike Fighters";
		$this->occurence = "common";
	$this->imagePath = "img/ships/Rotas.png";
	$this->unofficial = true;

    $this->notes = 'Only available to House Valheru.';

	$this->forwardDefense = 6;
	$this->sideDefense = 4;
	$this->freethrust = 12;
	$this->offensivebonus = 6;
	$this->jinkinglimit = 8;
	$this->turncost = 0.33;

	$this->isd = 2258;

	$this->iniativebonus = 18*5;
	$this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){

		$armour = array(4, 1, 3, 3);
		$fighter = new Fighter("Rotas", $armour, 11, $this->id);
		$fighter->displayName = "Rotas";
		$fighter->imagePath = "img/ships/Rotas.png";
		$fighter->iconPath = "img/ships/Rotas_large.png";
		
		$fighter->addFrontSystem(new PairedParticleGun(330, 30, 2));

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack

		$this->addSystem($fighter);

	}


    }

}
